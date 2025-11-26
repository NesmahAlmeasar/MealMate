@extends('layouts.admin_app')

@section('title', 'My Profile')

@push('styles')
<style>
    :root {
        --olive-dark: #6b7c28;
        --olive-medium: #8a9a3a;
        --olive-light: #b8c77d;
        --olive-very-light: #e8f0d5;
        --blue-primary: #3B82F6;
        --text-dark: #1f2937;
        --text-light: #6b7280;
        --border-color: #e5e7eb;
    }

    .profile-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .back-button-link {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        color: var(--text-light);
        text-decoration: none;
        font-size: 14px;
        transition: color 0.3s ease;
        margin-bottom: 15px;
    }

    .back-button-link:hover {
        color: var(--olive-dark);
    }

    .profile-title {
        font-size: 24px;
        font-weight: bold;
        color: var(--text-dark);
    }

    .profile-content {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 20px;
    }

    .profile-card {
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        border: 1px solid var(--border-color);
        text-align: center;
    }

    .profile-image {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--olive-dark), var(--olive-medium));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 60px;
        margin: 0 auto 15px;
        color: white;
        border: 4px solid var(--olive-light);
        overflow: hidden;
    }

    .profile-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-name {
        font-size: 22px;
        font-weight: bold;
        color: var(--text-dark);
        margin-bottom: 5px;
    }

    .profile-title-text {
        font-size: 14px;
        color: var(--text-light);
        margin-bottom: 15px;
    }

    .profile-stats {
        display: flex;
        justify-content: space-around;
        margin: 20px 0;
        padding: 15px 0;
        border-top: 1px solid var(--border-color);
        border-bottom: 1px solid var(--border-color);
    }

    .stat-item {
        text-align: center;
    }

    .stat-number {
        font-size: 20px;
        font-weight: bold;
        color: var(--olive-dark);
    }

    .stat-text {
        font-size: 12px;
        color: var(--text-light);
    }

    .profile-details {
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        border: 1px solid var(--border-color);
    }

    .details-section {
        margin-bottom: 25px;
    }

    .details-section:last-child {
        margin-bottom: 0;
    }

    .section-title {
        font-size: 16px;
        font-weight: bold;
        color: var(--text-dark);
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--olive-very-light);
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid var(--border-color);
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-weight: 500;
        color: var(--text-dark);
        font-size: 14px;
    }

    .detail-value {
        color: var(--text-light);
        font-size: 14px;
    }

    .profile-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    .profile-actions button {
        flex: 1;
        padding: 10px 15px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
        font-size: 14px;
    }

    .btn-edit {
        background-color: var(--blue-primary);
        color: white;
    }

    .btn-edit:hover {
        background-color: #2563EB;
    }

    /* Edit Form Styles */
    .edit-form {
        display: none;
    }

    .edit-form.active {
        display: block;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
        color: var(--text-dark);
        font-size: 14px;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid var(--border-color);
        border-radius: 5px;
        font-size: 14px;
    }

    .form-group textarea {
        min-height: 100px;
        resize: vertical;
    }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    .btn-save {
        background-color: var(--olive-dark);
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
    }

    .btn-cancel {
        background-color: #6c757d;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .profile-content {
            grid-template-columns: 1fr;
        }

        .profile-actions {
            flex-direction: column;
        }

        .profile-image {
            width: 120px;
            height: 120px;
            font-size: 50px;
        }
    }
</style>
@endpush

@section('content')
<!-- Back Button -->
<a href="{{ route('specialist.dashboard') }}" class="back-button-link">
    <i class="fas fa-arrow-left"></i> Back to Dashboard
</a>

<!-- Profile Header -->
<div class="profile-header">
    <h1 class="profile-title">My Profile</h1>
</div>

@if(session('success'))
    <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
@endif

<!-- Profile Content -->
<div class="profile-content">
    <!-- Profile Card -->
    <div class="profile-card">
        <div class="profile-image">
            @if($user->photo_url)
                <img src="{{ asset('storage/' . $user->photo_url) }}" alt="{{ $user->Fname }}">
            @else
                {{ strtoupper(substr($user->Fname, 0, 1)) }}
            @endif
        </div>
        <div class="profile-name">{{ $user->Fname }} {{ $user->Lname }}</div>
        <div class="profile-title-text">{{ $nutritionist->Academic_level ?? 'Nutrition Specialist' }}</div>

        <div class="profile-stats">
            <div class="stat-item">
                <div class="stat-number">0</div>
                <div class="stat-text">Clients</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $dietsCount }}</div>
                <div class="stat-text">Diets</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">5.0</div>
                <div class="stat-text">Rating</div>
            </div>
        </div>

        <div class="profile-actions">
            <button class="btn-edit" onclick="toggleEditMode()">
                <i class="fas fa-edit"></i> <span id="editBtnText">Edit Profile</span>
            </button>
        </div>
    </div>

    <!-- Profile Details -->
    <div class="profile-details">
        <!-- View Mode -->
        <div id="viewMode">
            <!-- Personal Information -->
            <div class="details-section">
                <div class="section-title">Personal Information</div>
                <div class="detail-row">
                    <div class="detail-label">Full Name</div>
                    <div class="detail-value">{{ $user->Fname }} {{ $user->Lname }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Email</div>
                    <div class="detail-value">{{ $user->email }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Phone</div>
                    <div class="detail-value">{{ $user->phone ?? 'Not provided' }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Account Status</div>
                    <div class="detail-value">{{ $user->account_state ?? 'Active' }}</div>
                </div>
            </div>

            <!-- Professional Information -->
            <div class="details-section">
                <div class="section-title">Professional Information</div>
                <div class="detail-row">
                    <div class="detail-label">Academic Level</div>
                    <div class="detail-value">{{ $nutritionist->Academic_level ?? 'Not specified' }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Total Diets Created</div>
                    <div class="detail-value">{{ $dietsCount }}</div>
                </div>
            </div>

            <!-- Bio -->
            @if($nutritionist->description)
            <div class="details-section">
                <div class="section-title">Bio</div>
                <p style="color: var(--text-light); font-size: 14px; line-height: 1.6;">
                    {{ $nutritionist->description }}
                </p>
            </div>
            @endif
        </div>

        <!-- Edit Mode -->
        <div id="editMode" class="edit-form">
            <form action="{{ route('specialist.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="details-section">
                    <div class="section-title">Personal Information</div>
                    
                    <div class="form-group">
                        <label for="Fname">First Name *</label>
                        <input type="text" id="Fname" name="Fname" value="{{ old('Fname', $user->Fname) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="Lname">Last Name *</label>
                        <input type="text" id="Lname" name="Lname" value="{{ old('Lname', $user->Lname) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                    </div>

                    <div class="form-group">
                        <label for="photo">Profile Photo</label>
                        <input type="file" id="photo" name="photo" accept="image/*">
                        <small style="color: var(--text-light);">Leave empty to keep current photo</small>
                    </div>
                </div>

                <div class="details-section">
                    <div class="section-title">Professional Information</div>
                    
                    <div class="form-group">
                        <label for="Academic_level">Academic Level</label>
                        <input type="text" id="Academic_level" name="Academic_level" value="{{ old('Academic_level', $nutritionist->Academic_level) }}" placeholder="e.g., PhD in Nutrition, MSc Clinical Nutrition">
                    </div>

                    <div class="form-group">
                        <label for="description">Bio / Description</label>
                        <textarea id="description" name="description" placeholder="Tell us about yourself, your experience, and specializations">{{ old('description', $nutritionist->description) }}</textarea>
                    </div>
                </div>

                <div class="details-section">
                    <div class="section-title">Change Password (Optional)</div>
                    
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input type="password" id="password" name="password" placeholder="Leave empty to keep current password">
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirm New Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <button type="button" class="btn-cancel" onclick="toggleEditMode()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleEditMode() {
        const viewMode = document.getElementById('viewMode');
        const editMode = document.getElementById('editMode');
        const editBtnText = document.getElementById('editBtnText');
        
        if (editMode.classList.contains('active')) {
            editMode.classList.remove('active');
            viewMode.style.display = 'block';
            editBtnText.textContent = 'Edit Profile';
        } else {
            editMode.classList.add('active');
            viewMode.style.display = 'none';
            editBtnText.textContent = 'Cancel Edit';
        }
    }
</script>
@endpush
@endsection
