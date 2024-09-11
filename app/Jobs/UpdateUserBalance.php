<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class UpdateUserBalance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $amount;

    /**
     * Create a new job instance.
     *
     * @param int $userId
     * @param float $amount
     */
    public function __construct(int $userId, float $amount)
    {
        $this->userId = $userId;
        $this->amount = $amount;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // 使用 Redis 锁来保证唯一性
        $lockKey = "user_balance_update_{$this->userId}";

        // 尝试获取锁，过期时间为10秒
        $lock = Cache::lock($lockKey, 10);

        if ($lock) {
            try {
                // 进行余额更新操作
                $user = User::find($this->userId);
                if ($user) {
                    $user->balance += $this->amount;
                    $user->save();
                }
            } finally {
                $lock->release();
            }
        }
    }
}
