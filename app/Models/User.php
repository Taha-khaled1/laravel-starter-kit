<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Scopes\LatestScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
  use HasApiTokens;

  /** @use HasFactory<\Database\Factories\UserFactory> */
  use HasFactory;
  use HasProfilePhoto;
  use Notifiable;
  use TwoFactorAuthenticatable;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $guarded = [];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'remember_token',
    'two_factor_recovery_codes',
    'two_factor_secret',
  ];

  /**
   * The accessors to append to the model's array form.
   *
   * @var array<int, string>
   */
  protected $appends = [
    'profile_photo_url',
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
      'language' => 'array',
    ];
  }



  public function hasType(string $type): bool
  {
    return $this->type === $type;
  }

  protected static function booted()
  {
    static::addGlobalScope(new LatestScope);  // Apply the LatestScope
  }


  public function country()
  {
    return $this->belongsTo(Country::class);
  }

  public function city()
  {
    return $this->belongsTo(City::class);
  }
  // One user can belong to many positions (pivot table)
  public function positions(): BelongsToMany
  {
    return $this->belongsToMany(Position::class, 'user_positions');
  }
  public function position()
  {
    return $this->belongsTo(Position::class);
  }
  /**
   * Get the user's events (that they created)
   */
  public function createdEvents()
  {
    return $this->hasMany(Event::class, 'created_by');
  }

  /**
   * Get the user's job applications
   */
  // app/Models/User.php

  public function events()
  {
    return $this->belongsToMany(Event::class, 'job_applications', 'user_id', 'event_id')
      ->withPivot([
        'status',
        'applied_at',
        'reviewed_at',
        'contract_agreed'
      ])
      ->withTimestamps();
  }

  public function jobApplications()
  {
    return $this->hasMany(JobApplication::class);
  }

  public function appliedJobPositions()
  {
    return $this->belongsToMany(JobPosition::class, 'job_applications', 'user_id', 'job_position_id')
      ->withPivot('status');
  }

  /**
   * Get all attendance records checked in by this supervisor
   */
  public function checkedIn()
  {
    return $this->hasMany(AttendanceRecord::class, 'checked_in_by');
  }

  /**
   * Get all attendance records checked out by this supervisor
   */
  public function checkedOut()
  {
    return $this->hasMany(AttendanceRecord::class, 'checked_out_by');
  }

  /**
   * Get ratings given by this user
   */
  public function givenRatings()
  {
    return $this->hasMany(Rating::class, 'rater_id');
  }

  /**
   * Get ratings received by this user
   */
  public function receivedRatings()
  {
    return $this->hasMany(Rating::class, 'rated_id');
  }
  public function getAverageRatingAttribute()
  {
    return $this->receivedRatings()->avg('rating') ?? 0;
  }
  /**
   * Get the payroll entries for the user.
   */
  public function payrollEntries()
  {
    return $this->hasMany(PayrollEntry::class);
  }

  /**
   * Get the payrolls created by the user.
   */
  public function createdPayrolls()
  {
    return $this->hasMany(Payroll::class, 'created_by');
  }

  /**
   * Get the payment transactions processed by the user.
   */
  public function processedTransactions()
  {
    return $this->hasMany(PaymentTransaction::class, 'processed_by');
  }

  public function chat()
  {
    return $this->belongsToMany(Chat::class, 'participants')->latest("last_message_id")->withPivot(["role"]);
  }

  public function sentMessages()
  {
    return $this->hasMany(Message::class, 'user_id', 'id');
  }


  public function receivedMessages()
  {
    return $this->hasMany(Message::class, 'recipients')->withPivot(["read_at"]);
  }
}
