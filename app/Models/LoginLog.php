<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // Import the User model


class LoginLog extends Model
{
    use HasFactory;

    protected $table ='login_logs';

    protected $fillable = [
        'user_id',
        'login_method',
        'is_successful',
        'ip_address',
        'details',
    ];

    protected $casts = [
        'is_successful' => 'boolean',
    ];

    // Define relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
