@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="page-header">
    <h1 class="page-title">My Profile</h1>
    <p class="page-subtitle">Manage your personal information and settings</p>
</div>

<div class="profile-container">
    {{-- Profile Card --}}
    <div class="card">
        <div class="profile-header-section">
            <div class="profile-photo-wrapper">
                <div class="profile-photo">
                    @if($user->photo_url)
                        <img src="{{ asset('storage/' . $user->photo_url) }}" alt="Profile Photo">
                    @else
                        <i class="fas fa-user fa-4x"></i>
                    @endif
                </div>
                <form method="POST" action="{{ route('profile.photo.update') }}" enctype="multipart/form-data" id="photoForm">
                    @csrf
                    @method('PUT')
                    <input type="file" name="photo" id="photoInput" accept="image/*" style="display: none;" onchange="document.getElementById('photoForm').submit()">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('photoInput').click()">
                        <i class="fas fa-camera"></i>
                        Change Photo
                    </button>
                </form>
            </div>
            
            <div class="profile-info-header">
                <h2>{{ $user->full_name }}</h2>
                <p>{{ $user->email }}</p>
                <div class="roles-display">
                    @foreach($user->roles as $role)
                        <span class="role-badge-large">{{ $role->name }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    
    {{-- Personal Information --}}
    <div class="card">
        <h3 class="card-title">Personal Information</h3>
        
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="Fname">First Name</label>
                    <input type="text" id="Fname" name="Fname" value="{{ old('Fname', $user->Fname) }}" required class="form-control">
                    @error('Fname')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="Lname">Last Name</label>
                    <input type="text" id="Lname" name="Lname" value="{{ old('Lname', $user->Lname) }}" required class="form-control">
                    @error('Lname')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required class="form-control">
                @error('email')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control">
                @error('phone')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                Save Changes
            </button>
        </form>
    </div>
    
    {{-- Change Password --}}
    <div class="card">
        <h3 class="card-title">Change Password</h3>
        
        <form method="POST" action="{{ route('profile.password.update') }}">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" required class="form-control">
                @error('current_password')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" id="password" name="password" required class="form-control">
                @error('password')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="password_confirmation">Confirm New Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required class="form-control">
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-key"></i>
                Update Password
            </button>
        </form>


        
    </div>
</div>
@endsection

@push('styles')
<style>
.profile-container {
    max-width: 800px;
}

.profile-header-section {
    display: flex;
    gap: 30px;
    align-items: center;
}

.profile-photo-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 15px;
}

.profile-photo {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    overflow: hidden;
}

.profile-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-info-header h2 {
    font-size: 24px;
    margin-bottom: 8px;
}

.profile-info-header p {
    color: #6b7280;
    margin-bottom: 12px;
}

.roles-display {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.role-badge-large {
    padding: 6px 12px;
    background: #e0e7ff;
    color: #667eea;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
}

.card-title {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f3f4f6;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #374151;
}

.form-control {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
}

.error-text {
    color: #dc2626;
    font-size: 12px;
    margin-top: 4px;
    display: block;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
}

@media (max-width: 768px) {
    .profile-header-section {
        flex-direction: column;
        text-align: center;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush
