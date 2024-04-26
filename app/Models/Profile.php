<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

/**
 * 用户资料模型
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * 
 * @property int $id 用户主键
 * @property string $user_id 用户主体ID
 * @property ?string $birthday_card_id 生日卡样式
 * @property ?string $name 姓名
 * @property ?string $pinyin 拼音姓名
 * @property int $gender 性别
 * @property ?Carbon $birth 生日
 * @property ?string $nation 民族
 * @property ?string $birthplace 籍贯
 * @property ?string $address 家庭地址
 * @property ?string $id_card 证件号码
 * @property ?string $educational 教育学历
 * @property ?string $science 学位
 * @property ?string $emergency_contact 紧急联系人
 * @property ?string $emergency_telephone 紧急联系电话
 * @property int $skill_level 综合技能等级
 * @property int $career_level 职业等级
 * @property ?array $vocational_skills 职业技能
 * @property ?Carbon $attend_date 参加工作时间
 * @property ?Carbon $entry_date 入职时间
 * @property ?Carbon $created_at 首次完善时间
 * @property ?Carbon $updated_at 更新时间
 * @property-read User $user 用户主体
 * @property-read BirthdayCard $birthday_card 用户主体
 * @property-read ?string $birth_image 生日卡片
 * @property-read ?string $avatar 用户头像
 * @property-read ?Collection<Media> $media 头像附件
 */
class Profile extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * 头像主键
     */
    const MEDIA_FILE = 'avatar';

    /**
     * 生日卡片
     */
    const MEDIA_BIRTH = 'birth';

    protected $fillable = [
        'user_id',
        'birthday_card_id',
        'name',
        'pinyin',
        'gender',
        'birth',
        'nation',
        'birthplace',
        'address',
        'id_card',
        'educational',
        'science',
        'emergency_contact',
        'emergency_telephone',
        'skill_level',
        'career_level',
        'attend_date',
        'entry_date',
        'vocational_skills'
    ];

    public $casts = [
        'gender' => 'integer',
        'skill_level' => 'integer',
        'career_level' => 'integer',
        'birth' => 'datetime',
        'attend_date' => 'datetime',
        'entry_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'vocational_skills' => 'array'
    ];

    public $appends = ['avatar','birth_image'];

    public $hidden = ['media'];

    /**
     * 用户
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<User>|User|Model
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 生日卡
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<BirthdayCard>|BirthdayCard|Model
     */
    public function birthday_card()
    {
        return $this->belongsTo(BirthdayCard::class);
    }

    public function getAvatarAttribute()
    {
        return $this->getFirstMediaUrl(self::MEDIA_FILE);
    }

    public function getBirthImageAttribute()
    {
        return $this->getFirstMediaUrl(self::MEDIA_BIRTH);
    }
}
