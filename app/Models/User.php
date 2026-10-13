<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Các trường được phép gán dữ liệu hàng loạt.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role_id',
        'name',
        'email',
        'google_id',
        'phone',
        'avatar',
        'position',
        'is_active',
        'password',
    ];

    /**
     * Các trường không hiển thị khi serialize dữ liệu.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Ép kiểu dữ liệu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * Một User thuộc một Role.
     *
     * Role 1 - N User
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Một User có thể có nhiều địa chỉ.
     *
     * User 1 - N Address
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    /**
 * Một User có thể lưu nhiều thiết kế Studio.
 */
public function customDesigns()
{
    return $this->hasMany(CustomDesign::class);
}

/**
 * Một User có một Wishlist.
 */
public function wishlist()
{
    return $this->hasOne(\App\Models\Wishlist::class);
}
/**
 * Một User có một Cart.
 */
public function cart()
{
    return $this->hasOne(Cart::class);
}
/**
 * Một User có nhiều Order.
 */
public function orders()
{
    return $this->hasMany(Order::class);
}
/**
 * Các lần User sử dụng Voucher.
 */
public function voucherUsages()
{
    return $this->hasMany(VoucherUsage::class);
}

/**
 * Các Voucher Customer đã lưu để dùng sau.
 */
public function savedVouchers()
{
    return $this->belongsToMany(
        Voucher::class,
        'saved_vouchers'
    )->withTimestamps();
}
/**
 * Các thay đổi kho do User/Staff/Admin thực hiện.
 */
public function stockHistories()
{
    return $this->hasMany(
        StockHistory::class,
        'updated_by'
    );
}
/**
 * Các Review do Customer viết.
 */
public function reviews()
{
    return $this->hasMany(Review::class);
}

/**
 * Các phản hồi Review do Staff/Admin viết.
 */
public function reviewReplies()
{
    return $this->hasMany(ReviewReply::class);
}
/**
 * Các My Grove thuộc User.
 */
public function groves()
{
    return $this->hasMany(Grove::class);
}
/**
 * Các QR Token do Staff/Admin sinh.
 */
public function generatedGroveQrTokens()
{
    return $this->hasMany(
        GroveQrToken::class,
        'generated_by'
    );
}
/**
 * Các hoạt động chăm sóc do User thực hiện.
 */
public function performedCareLogs()
{
    return $this->hasMany(
        CareLog::class,
        'performed_by'
    );
}
/**
 * Các thông báo trong hệ thống của User.
 */
public function appNotifications()
{
    return $this->hasMany(UserNotification::class);
}
/**
 * Các Audit Log do User thực hiện.
 */
public function auditLogs()
{
    return $this->hasMany(AuditLog::class);
}
/**
 * Growth Journal do Customer tạo.
 */
public function growthJournals()
{
    return $this->hasMany(GrowthJournal::class);
}
/**
 * Các Eco Rescue Request do Staff/Admin xử lý.
 */
public function handledEcoRescueRequests()
{
    return $this->hasMany(
        EcoRescueRequest::class,
        'handled_by'
    );
}
/**
 * Các Maintenance Request do Staff/Admin xử lý.
 */
public function handledMaintenanceRequests()
{
    return $this->hasMany(
        MaintenanceRequest::class,
        'handled_by'
    );
}
/**
 * Các bảo hành thuộc Customer.
 */
public function warranties()
{
    return $this->hasMany(Warranty::class);
}

/**
 * Các Warranty Request do Staff/Admin xử lý.
 */
public function handledWarrantyRequests()
{
    return $this->hasMany(
        WarrantyRequest::class,
        'handled_by'
    );
}
/**
 * Ví Grove Point của User.
 */
public function pointWallet()
{
    return $this->hasOne(PointWallet::class);
}

/**
 * Lịch sử Grove Point của User.
 */
public function pointTransactions()
{
    return $this->hasMany(PointTransaction::class);
}

/**
 * Các điều chỉnh Point do Staff/Admin thực hiện.
 */
public function performedPointTransactions()
{
    return $this->hasMany(
        PointTransaction::class,
        'performed_by'
    );
}
/**
 * Lịch sử đổi Grove Point lấy Reward.
 */
public function rewardRedemptions()
{
    return $this->hasMany(RewardRedemption::class);
}

/**
 * Các Redemption do Staff/Admin xử lý.
 */
public function handledRewardRedemptions()
{
    return $this->hasMany(
        RewardRedemption::class,
        'handled_by'
    );
}
/**
 * Các Green Return do Customer gửi.
 */
public function greenReturnRequests()
{
    return $this->hasMany(GreenReturnRequest::class);
}

/**
 * Các Green Return do Staff/Admin xử lý.
 */
public function handledGreenReturnRequests()
{
    return $this->hasMany(
        GreenReturnRequest::class,
        'handled_by'
    );
}

/**
 * Các Green Return được User này phát thưởng.
 */
public function rewardedGreenReturnRequests()
{
    return $this->hasMany(
        GreenReturnRequest::class,
        'rewarded_by'
    );
}
/**
 * Các bài Community do User đăng.
 */
public function communityPosts()
{
    return $this->hasMany(CommunityPost::class);
}

/**
 * Các Like của User.
 */
public function postLikes()
{
    return $this->hasMany(PostLike::class);
}

/**
 * Các Comment của User.
 */
public function postComments()
{
    return $this->hasMany(PostComment::class);
}

/**
 * Những quan hệ Follow do User thực hiện.
 */
public function followingRelations()
{
    return $this->hasMany(
        UserFollow::class,
        'follower_id'
    );
}

/**
 * Những quan hệ User khác Follow User này.
 */
public function followerRelations()
{
    return $this->hasMany(
        UserFollow::class,
        'following_id'
    );
}
/**
 * Các Challenge do Staff/Admin tạo.
 */
public function createdChallenges()
{
    return $this->hasMany(
        Challenge::class,
        'created_by'
    );
}

/**
 * Các Challenge User đã tham gia.
 */
public function challengeParticipations()
{
    return $this->hasMany(ChallengeParticipant::class);
}
/**
 * Các Conversation Customer đã tạo.
 */
public function customerConversations()
{
    return $this->hasMany(
        Conversation::class,
        'customer_id'
    );
}

/**
 * Các Conversation Admin được phân công.
 */
public function assignedConversations()
{
    return $this->hasMany(
        Conversation::class,
        'assigned_admin_id'
    );
}

/**
 * Các Message User đã gửi.
 */
public function messages()
{
    return $this->hasMany(
        Message::class,
        'sender_id'
    );
}

/**
 * Các Conversation do User đóng.
 */
public function closedConversations()
{
    return $this->hasMany(
        Conversation::class,
        'closed_by'
    );
}
/**
 * Các Article do Staff/Admin viết.
 */
public function authoredArticles()
{
    return $this->hasMany(
        Article::class,
        'author_id'
    );
}

/**
 * Các Article do User này ẩn.
 */
public function hiddenArticles()
{
    return $this->hasMany(
        Article::class,
        'hidden_by'
    );
}
/**
 * Các GroveMatch Quiz User đã thực hiện.
 */
public function quizSessions()
{
    return $this->hasMany(QuizSession::class);
}
/**
 * Các yêu cầu trả hàng
 * của Customer.
 */
public function returnRequests()
{
    return $this->hasMany(
        ReturnRequest::class
    );
}


/**
 * Các yêu cầu trả hàng
 * User đã xử lý với vai trò
 * Staff/Admin.
 */
public function processedReturnRequests()
{
    return $this->hasMany(
        ReturnRequest::class,
        'processed_by'
    );
}

public function sendPasswordResetNotification($token)
{
    $testEmail = config('mail.admin_order_email');

    if (
        $testEmail &&
        strcasecmp($this->email, $testEmail) === 0
    ) {
        $this->notify(
            new \App\Notifications\ResendPasswordReset($token)
        );

        return;
    }

    $this->notify(
        new \Illuminate\Auth\Notifications\ResetPassword($token)
    );
}

}