@extends('core::layouts.app')

@section('title', 'General Clinic Settings')

@section('content')
<div class="page-header" style="margin-bottom: 1.25rem;">
    <div>
        <h1 class="page-title">Clinic Settings</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">
            Configure clinic branding, titles, contact credentials, and doctor income exclusion rules
        </p>
    </div>
</div>

@if(session('success'))
    <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 0.875rem 1.25rem; border-radius: 8px; margin-bottom: 1.25rem; font-size: 0.875rem; display: flex; align-items: center; gap: 8px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if ($errors->any())
    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 0.875rem 1.25rem; border-radius: 8px; margin-bottom: 1.25rem; font-size: 0.875rem;">
        <strong style="display: block; margin-bottom: 4px;">Please review the following errors:</strong>
        <ul style="margin: 0; padding-left: 1.25rem;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Tabs Navigation -->
<div style="display: flex; gap: 4px; border-bottom: 2px solid #e2e8f0; margin-bottom: 1.5rem;">
    <button type="button" class="tab-btn {{ $activeTab === 'clinic_info' ? 'active-tab' : '' }}" onclick="switchTab('clinic_info')" id="tabBtnClinicInfo" style="padding: 10px 20px; font-size: 0.9375rem; font-weight: 700; border: none; background: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; border-bottom: 3px solid {{ $activeTab === 'clinic_info' ? 'var(--primary)' : 'transparent' }}; color: {{ $activeTab === 'clinic_info' ? 'var(--primary)' : '#64748b' }}; margin-bottom: -2px; transition: all 0.15s;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            <polyline points="9 22 9 12 15 12 15 22"></polyline>
        </svg>
        <span>Clinic Info</span>
    </button>
    <button type="button" class="tab-btn {{ $activeTab === 'except_doctor' ? 'active-tab' : '' }}" onclick="switchTab('except_doctor')" id="tabBtnExceptDoctor" style="padding: 10px 20px; font-size: 0.9375rem; font-weight: 700; border: none; background: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; border-bottom: 3px solid {{ $activeTab === 'except_doctor' ? 'var(--primary)' : 'transparent' }}; color: {{ $activeTab === 'except_doctor' ? 'var(--primary)' : '#64748b' }}; margin-bottom: -2px; transition: all 0.15s;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="8.5" cy="7" r="4"></circle>
            <line x1="18" y1="8" x2="23" y2="13"></line>
            <line x1="23" y1="8" x2="18" y2="13"></line>
        </svg>
        <span>Except Income Doctor</span>
        @php
            $excludedCount = count($setting->except_doctor_ids ?? []);
        @endphp
        <span id="tabExcludedBadge" class="badge" style="background: {{ $excludedCount > 0 ? '#fee2e2' : '#f1f5f9' }}; color: {{ $excludedCount > 0 ? '#dc2626' : '#64748b' }}; font-size: 0.75rem; padding: 2px 7px; border-radius: 999px;">
            {{ $excludedCount }}
        </span>
    </button>
</div>

<form method="POST" action="{{ route('settings.general.update') }}" enctype="multipart/form-data" id="settingsForm">
    @csrf
    @method('PUT')
    <input type="hidden" name="active_tab" id="activeTabInput" value="{{ old('active_tab', $activeTab) }}">
    <input type="hidden" name="remove_logo" id="removeLogoInput" value="0">

    <!-- ==================== TAB 1: CLINIC INFO ==================== -->
    <div id="tabClinicInfo" style="display: {{ $activeTab === 'clinic_info' ? 'block' : 'none' }};">
        <div style="display: grid; grid-template-columns: 290px 1fr; gap: 1.5rem; align-items: start;">
            
            <!-- Left Column: Clinic Logo Card -->
            <div class="card" style="display: flex; flex-direction: column; gap: 1rem;">
                <div>
                    <h2 style="font-size: 1.05rem; font-weight: 700; color: #0f172a; margin: 0 0 4px;">Clinic Logo</h2>
                    <p style="font-size: 0.75rem; color: #64748b; margin: 0;">
                        Appears on dental charts, invoices, receipts, and clinical reports
                    </p>
                </div>

                <!-- Logo Preview Box -->
                <div id="logoPreviewWrapper" style="border: 2px dashed #cbd5e1; border-radius: 12px; background: #f8fafc; height: 180px; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden; padding: 12px; text-align: center;">
                    <img id="logoPreviewImg" 
                         src="{{ $setting->logo_url ?? '' }}" 
                         alt="Clinic Logo Preview" 
                         style="max-height: 100%; max-width: 100%; object-fit: contain; display: {{ $setting->logo_path ? 'block' : 'none' }};">

                    <div id="logoPlaceholder" style="display: {{ $setting->logo_path ? 'none' : 'flex' }}; flex-direction: column; align-items: center; justify-content: center; color: #94a3b8; gap: 6px;">
                        <svg width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                        <span style="font-size: 0.8125rem; font-weight: 600;">No Clinic Logo</span>
                        <span style="font-size: 0.7rem; color: #cbd5e1;">PNG, JPG, WEBP</span>
                    </div>
                </div>

                <!-- Logo Actions -->
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <label for="logoFileInput" class="btn btn-outline" style="width: 100%; justify-content: center; cursor: pointer; font-size: 0.8125rem;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px;">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                        Upload New Logo
                    </label>
                    <input type="file" name="logo" id="logoFileInput" accept="image/png,image/jpeg,image/webp" style="display: none;">

                    <button type="button" id="btnRemoveLogo" class="btn btn-outline" style="width: 100%; justify-content: center; font-size: 0.8125rem; color: #ef4444; border-color: #fecaca; display: {{ $setting->logo_path ? 'inline-flex' : 'none' }};" onclick="handleRemoveLogo()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px;">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                        Remove Logo
                    </button>
                </div>

                <div style="background: #f1f5f9; border-radius: 8px; padding: 10px; font-size: 0.725rem; color: #64748b; line-height: 1.4;">
                    <strong>Guidelines:</strong> Recommended minimum dimensions 250×250px. Max size 2MB. Transparent PNG recommended.
                </div>
            </div>

            <!-- Right Column: Clinic Identity & Contact Credentials -->
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                <div class="card" style="display: flex; flex-direction: column; gap: 1.15rem;">
                    <div>
                        <h2 style="font-size: 1.05rem; font-weight: 700; color: #0f172a; margin: 0 0 4px;">Clinic Identity</h2>
                        <p style="font-size: 0.75rem; color: #64748b; margin: 0;">Multi-language titles, description, and custom color accents</p>
                    </div>

                    <!-- B. Khmer Title & Color -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="khmer_title" style="display: flex; justify-content: space-between; font-weight: 600; font-size: 0.8125rem; margin-bottom: 4px;">
                            <span>Khmer Title (ចំណងជើងជាភាសាខ្មែរ)</span>
                            <span style="font-size: 0.75rem; color: #64748b;">Text Color</span>
                        </label>
                        <div style="display: grid; grid-template-columns: 1fr 180px; gap: 10px; align-items: center;">
                            <input type="text" name="khmer_title" id="khmer_title" 
                                   class="form-control" 
                                   value="{{ old('khmer_title', $setting->khmer_title) }}" 
                                   placeholder="e.g. គ្លីនិកធ្មេញ អូដុនតូខែរ" 
                                   style="font-family: inherit; font-size: 0.875rem;">
                            
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <input type="color" id="khmer_title_color_picker" 
                                       value="{{ old('khmer_title_color', $setting->khmer_title_color ?? '#000000') }}" 
                                       style="width: 38px; height: 38px; padding: 2px; border: 1px solid #cbd5e1; border-radius: 6px; cursor: pointer;"
                                       oninput="syncColor('khmer_title_color', this.value)">
                                <input type="text" name="khmer_title_color" id="khmer_title_color" 
                                       class="form-control" 
                                       value="{{ old('khmer_title_color', $setting->khmer_title_color ?? '#000000') }}" 
                                       maxlength="7" 
                                       style="font-family: monospace; font-size: 0.8125rem; text-transform: uppercase;"
                                       oninput="syncColorPicker('khmer_title_color_picker', this.value)">
                            </div>
                        </div>
                    </div>

                    <!-- C. English Title & Color -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="english_title" style="display: flex; justify-content: space-between; font-weight: 600; font-size: 0.8125rem; margin-bottom: 4px;">
                            <span>English Title</span>
                            <span style="font-size: 0.75rem; color: #64748b;">Text Color</span>
                        </label>
                        <div style="display: grid; grid-template-columns: 1fr 180px; gap: 10px; align-items: center;">
                            <input type="text" name="english_title" id="english_title" 
                                   class="form-control" 
                                   value="{{ old('english_title', $setting->english_title) }}" 
                                   placeholder="e.g. OdontoCare Dental Clinic" 
                                   style="font-size: 0.875rem;">
                            
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <input type="color" id="english_title_color_picker" 
                                       value="{{ old('english_title_color', $setting->english_title_color ?? '#000000') }}" 
                                       style="width: 38px; height: 38px; padding: 2px; border: 1px solid #cbd5e1; border-radius: 6px; cursor: pointer;"
                                       oninput="syncColor('english_title_color', this.value)">
                                <input type="text" name="english_title_color" id="english_title_color" 
                                       class="form-control" 
                                       value="{{ old('english_title_color', $setting->english_title_color ?? '#000000') }}" 
                                       maxlength="7" 
                                       style="font-family: monospace; font-size: 0.8125rem; text-transform: uppercase;"
                                       oninput="syncColorPicker('english_title_color_picker', this.value)">
                            </div>
                        </div>
                    </div>

                    <!-- D. Description & Color -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="description" style="display: flex; justify-content: space-between; font-weight: 600; font-size: 0.8125rem; margin-bottom: 4px;">
                            <span>Description / Slogan</span>
                            <span style="font-size: 0.75rem; color: #64748b;">Text Color</span>
                        </label>
                        <div style="display: grid; grid-template-columns: 1fr 180px; gap: 10px; align-items: center;">
                            <input type="text" name="description" id="description" 
                                   class="form-control" 
                                   value="{{ old('description', $setting->description) }}" 
                                   placeholder="e.g. Gentle &amp; High Quality Dental Services" 
                                   style="font-size: 0.875rem;">
                            
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <input type="color" id="description_color_picker" 
                                       value="{{ old('description_color', $setting->description_color ?? '#555555') }}" 
                                       style="width: 38px; height: 38px; padding: 2px; border: 1px solid #cbd5e1; border-radius: 6px; cursor: pointer;"
                                       oninput="syncColor('description_color', this.value)">
                                <input type="text" name="description_color" id="description_color" 
                                       class="form-control" 
                                       value="{{ old('description_color', $setting->description_color ?? '#555555') }}" 
                                       maxlength="7" 
                                       style="font-family: monospace; font-size: 0.8125rem; text-transform: uppercase;"
                                       oninput="syncColorPicker('description_color_picker', this.value)">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact & Social Details Card -->
                <div class="card" style="display: flex; flex-direction: column; gap: 1.15rem;">
                    <div>
                        <h2 style="font-size: 1.05rem; font-weight: 700; color: #0f172a; margin: 0 0 4px;">Contact &amp; Social Information</h2>
                        <p style="font-size: 0.75rem; color: #64748b; margin: 0;">Details displayed on patient documents, receipts, and invoices</p>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <!-- E. Facebook Account -->
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="facebook_account" style="font-weight: 600; font-size: 0.8125rem; margin-bottom: 4px; display: block;">
                                Facebook Account
                            </label>
                            <input type="text" name="facebook_account" id="facebook_account" 
                                   class="form-control" 
                                   value="{{ old('facebook_account', $setting->facebook_account) }}" 
                                   placeholder="e.g. OdontoCare Dental Clinic">
                        </div>

                        <!-- F. Facebook Page -->
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="facebook_page" style="font-weight: 600; font-size: 0.8125rem; margin-bottom: 4px; display: block;">
                                Facebook Page URL
                            </label>
                            <input type="url" name="facebook_page" id="facebook_page" 
                                   class="form-control" 
                                   value="{{ old('facebook_page', $setting->facebook_page) }}" 
                                   placeholder="https://facebook.com/yourclinicpage">
                        </div>

                        <!-- G. Email -->
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="email" style="font-weight: 600; font-size: 0.8125rem; margin-bottom: 4px; display: block;">
                                Clinic Email
                            </label>
                            <input type="email" name="email" id="email" 
                                   class="form-control" 
                                   value="{{ old('email', $setting->email) }}" 
                                   placeholder="e.g. info@clinic.com">
                        </div>

                        <!-- H. Phone (+855) -->
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="phone" style="font-weight: 600; font-size: 0.8125rem; margin-bottom: 4px; display: block;">
                                Phone (+855)
                            </label>
                            <input type="text" name="phone" id="phone" 
                                   class="form-control" 
                                   value="{{ old('phone', $setting->phone) }}" 
                                   placeholder="e.g. +855 23 888 999 / 012 345 678">
                        </div>
                    </div>

                    <!-- I. Address (Rich-Text Editor) -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="font-weight: 600; font-size: 0.8125rem; margin-bottom: 4px; display: block;">
                            Clinic Address (Supports Khmer &amp; English formatting)
                        </label>
                        
                        <!-- Rich Text Toolbar -->
                        <div style="display: flex; gap: 4px; flex-wrap: wrap; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px 8px 0 0; padding: 6px 8px; border-bottom: 1px solid #e2e8f0;">
                            <button type="button" class="btn-rte" onclick="execRte('bold')" title="Bold (Ctrl+B)"><strong>B</strong></button>
                            <button type="button" class="btn-rte" onclick="execRte('italic')" title="Italic (Ctrl+I)"><em>I</em></button>
                            <button type="button" class="btn-rte" onclick="execRte('underline')" title="Underline (Ctrl+U)"><u>U</u></button>
                            <span style="width: 1px; height: 22px; background: #cbd5e1; margin: 0 4px; align-self: center;"></span>
                            
                            <select onchange="execRteFormatBlock(this.value); this.selectedIndex=0;" style="height: 28px; font-size: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0 6px; background: #fff; cursor: pointer;">
                                <option value="">Paragraph Style</option>
                                <option value="p">Normal (p)</option>
                                <option value="h3">Heading (h3)</option>
                                <option value="h4">Sub-heading (h4)</option>
                                <option value="blockquote">Quote</option>
                            </select>

                            <span style="width: 1px; height: 22px; background: #cbd5e1; margin: 0 4px; align-self: center;"></span>
                            <button type="button" class="btn-rte" onclick="execRte('justifyLeft')" title="Align Left">⇤</button>
                            <button type="button" class="btn-rte" onclick="execRte('justifyCenter')" title="Align Center">≡</button>
                            <button type="button" class="btn-rte" onclick="execRte('justifyRight')" title="Align Right">⇥</button>
                            
                            <span style="width: 1px; height: 22px; background: #cbd5e1; margin: 0 4px; align-self: center;"></span>
                            <button type="button" class="btn-rte" onclick="execRte('insertUnorderedList')" title="Bullet List">• List</button>
                            <button type="button" class="btn-rte" onclick="execRte('insertOrderedList')" title="Numbered List">1. List</button>
                            
                            <span style="width: 1px; height: 22px; background: #cbd5e1; margin: 0 4px; align-self: center;"></span>
                            <button type="button" class="btn-rte" onclick="execRteLink()" title="Insert Link">🔗 Link</button>
                            <button type="button" class="btn-rte" onclick="execRte('removeFormat')" title="Clear Format">🧹</button>
                        </div>

                        <!-- Editable Content Area -->
                        <div id="addressEditor" 
                             contenteditable="true" 
                             style="min-height: 120px; max-height: 260px; overflow-y: auto; background: #ffffff; border: 1px solid #cbd5e1; border-top: none; border-radius: 0 0 8px 8px; padding: 12px; font-size: 0.875rem; line-height: 1.6; outline: none;">
                            {!! old('address', $setting->address) !!}
                        </div>

                        <textarea name="address" id="addressInput" style="display: none;">{!! old('address', $setting->address) !!}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== TAB 2: EXCEPT INCOME DOCTOR ==================== -->
    <div id="tabExceptDoctor" style="display: {{ $activeTab === 'except_doctor' ? 'block' : 'none' }};">
        <div class="card" style="display: flex; flex-direction: column; gap: 1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 12px;">
                <div>
                    <h2 style="font-size: 1.15rem; font-weight: 700; color: #0f172a; margin: 0 0 4px;">Doctor Income Exclusion Configuration</h2>
                    <p style="font-size: 0.8125rem; color: #64748b; margin: 0;">
                        Checked doctors are excluded from clinic gross/net financial calculations and revenue share metrics
                    </p>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button type="button" class="btn btn-outline btn-sm" onclick="toggleAllDoctors(true)">Select All</button>
                    <button type="button" class="btn btn-outline btn-sm" onclick="toggleAllDoctors(false)">Deselect All</button>
                </div>
            </div>

            <!-- Search Filter Bar -->
            <div style="display: flex; gap: 10px; align-items: center;">
                <input type="text" id="doctorSearchInput" class="form-control" placeholder="Search doctor by name, username, or role..." style="max-width: 360px;" oninput="filterDoctorRows(this.value)">
                <span id="doctorCountStatus" style="font-size: 0.8125rem; color: #64748b; font-weight: 600;">
                    Showing {{ $doctors->count() }} doctors
                </span>
            </div>

            <!-- Notice Alert -->
            <div style="background: #fffbeb; border: 1px solid #fef3c7; border-radius: 8px; padding: 10px 14px; font-size: 0.8125rem; color: #92400e; display: flex; align-items: center; gap: 8px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <span>
                    <strong>Financial Exclusion Rule:</strong> Invoices or patient treatment procedures performed by excluded doctors will be segregated from total clinic revenue settlement reports.
                </span>
            </div>

            <!-- Doctors Table -->
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">Exclude</th>
                            <th>Doctor / Practitioner</th>
                            <th>Username</th>
                            <th>Role / Title</th>
                            <th>Contact Email</th>
                            <th style="text-align: center;">Status</th>
                        </tr>
                    </thead>
                    <tbody id="doctorsTableBody">
                        @forelse($doctors as $doctor)
                            @php
                                $isExcluded = $setting->isDoctorExcluded($doctor->id);
                            @endphp
                            <tr class="doctor-row" data-search="{{ strtolower($doctor->name . ' ' . $doctor->username . ' ' . $doctor->email . ' ' . $doctor->roles->pluck('label')->join(' ')) }}">
                                <td style="text-align: center;">
                                    <input type="checkbox" 
                                           name="except_doctor_ids[]" 
                                           value="{{ $doctor->id }}" 
                                           class="doctor-checkbox"
                                           {{ $isExcluded ? 'checked' : '' }} 
                                           onchange="updateExcludedBadgeCount()"
                                           style="width: 17px; height: 17px; cursor: pointer; accent-color: #dc2626;">
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 32px; height: 32px; border-radius: 50%; background: #e0f2fe; color: #0369a1; font-weight: 700; font-size: 0.75rem; display: flex; align-items: center; justify-content: center;">
                                            {{ strtoupper(substr($doctor->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <strong style="color: #0f172a; font-size: 0.875rem;">{{ $doctor->name }}</strong>
                                            @if($doctor->is_super_admin)
                                                <span class="badge" style="background: #fef3c7; color: #92400e; font-size: 0.65rem; margin-left: 4px;">Super Admin</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <code style="font-size: 0.8125rem; color: #475569;">{{ $doctor->username }}</code>
                                </td>
                                <td>
                                    @forelse($doctor->roles as $role)
                                        <span class="badge" style="background: #f1f5f9; color: #334155; font-size: 0.75rem; margin-right: 4px;">
                                            {{ $role->label }}
                                        </span>
                                    @empty
                                        <span style="color: #94a3b8; font-size: 0.8125rem;">Staff</span>
                                    @endforelse
                                </td>
                                <td>
                                    <span style="font-size: 0.8125rem; color: #64748b;">{{ $doctor->email }}</span>
                                </td>
                                <td style="text-align: center;">
                                    <span class="badge" style="background: #dcfce7; color: #166534; font-size: 0.75rem;">Active</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: #94a3b8; padding: 2rem;">
                                    No doctor records found in system.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ==================== STICKY FOOTER ACTIONS ==================== -->
    <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end; gap: 10px; background: #fff; padding: 14px 20px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
        <a href="{{ route('settings.general') }}" class="btn btn-outline" style="min-width: 110px; justify-content: center;">
            Cancel / Reload
        </a>
        <button type="submit" id="btnSubmitSettings" class="btn btn-primary" style="min-width: 140px; justify-content: center; gap: 6px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                <polyline points="7 3 7 8 15 8"></polyline>
            </svg>
            <span id="btnSubmitText">Save Settings</span>
        </button>
    </div>
</form>

<style>
.btn-rte {
    background: #fff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 3px 8px;
    font-size: 0.8125rem;
    font-weight: 600;
    color: #334155;
    cursor: pointer;
    min-width: 26px;
    height: 28px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: background 0.1s;
}
.btn-rte:hover {
    background: #e2e8f0;
}
.btn-rte:active {
    background: #cbd5e1;
}
@media (max-width: 860px) {
    #tabClinicInfo > div {
        grid-template-columns: 1fr !important;
    }
}
</style>

<script>
// Tab Switching
function switchTab(tabName) {
    const tabInfo = document.getElementById('tabClinicInfo');
    const tabDoc = document.getElementById('tabExceptDoctor');
    const btnInfo = document.getElementById('tabBtnClinicInfo');
    const btnDoc = document.getElementById('tabBtnExceptDoctor');
    const activeTabInput = document.getElementById('activeTabInput');

    activeTabInput.value = tabName;

    if (tabName === 'clinic_info') {
        tabInfo.style.display = 'block';
        tabDoc.style.display = 'none';

        btnInfo.style.borderColor = 'var(--primary)';
        btnInfo.style.color = 'var(--primary)';

        btnDoc.style.borderColor = 'transparent';
        btnDoc.style.color = '#64748b';
    } else {
        tabInfo.style.display = 'none';
        tabDoc.style.display = 'block';

        btnDoc.style.borderColor = 'var(--primary)';
        btnDoc.style.color = 'var(--primary)';

        btnInfo.style.borderColor = 'transparent';
        btnInfo.style.color = '#64748b';
    }
}

// Logo Handling: Preview, Replace, and Remove
const logoFileInput = document.getElementById('logoFileInput');
const logoPreviewImg = document.getElementById('logoPreviewImg');
const logoPlaceholder = document.getElementById('logoPlaceholder');
const btnRemoveLogo = document.getElementById('btnRemoveLogo');
const removeLogoInput = document.getElementById('removeLogoInput');

if (logoFileInput) {
    logoFileInput.addEventListener('change', function (e) {
        const file = this.files && this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (evt) {
                logoPreviewImg.src = evt.target.result;
                logoPreviewImg.style.display = 'block';
                logoPlaceholder.style.display = 'none';
                btnRemoveLogo.style.display = 'inline-flex';
                removeLogoInput.value = '0';
            };
            reader.readAsDataURL(file);
        }
    });
}

function handleRemoveLogo() {
    if (confirm('Are you sure you want to remove the clinic logo?')) {
        logoFileInput.value = '';
        logoPreviewImg.src = '';
        logoPreviewImg.style.display = 'none';
        logoPlaceholder.style.display = 'flex';
        btnRemoveLogo.style.display = 'none';
        removeLogoInput.value = '1';
    }
}

// Color Sync
function syncColor(targetInputId, value) {
    const el = document.getElementById(targetInputId);
    if (el) el.value = value.toUpperCase();
}

function syncColorPicker(targetPickerId, value) {
    const picker = document.getElementById(targetPickerId);
    if (picker && /^#[0-9A-F]{6}$/i.test(value)) {
        picker.value = value;
    }
}

// Rich Text Editor Helpers
function execRte(command, value = null) {
    document.execCommand(command, false, value);
    syncAddressContent();
}

function execRteFormatBlock(blockTag) {
    if (!blockTag) return;
    document.execCommand('formatBlock', false, blockTag);
    syncAddressContent();
}

function execRteLink() {
    const url = prompt('Enter website link URL:', 'https://');
    if (url) {
        document.execCommand('createLink', false, url);
        syncAddressContent();
    }
}

function syncAddressContent() {
    const editor = document.getElementById('addressEditor');
    const input = document.getElementById('addressInput');
    if (editor && input) {
        input.value = editor.innerHTML;
    }
}

const addressEditor = document.getElementById('addressEditor');
if (addressEditor) {
    addressEditor.addEventListener('input', syncAddressContent);
    addressEditor.addEventListener('blur', syncAddressContent);
}

// Form Submit Sync
document.getElementById('settingsForm').addEventListener('submit', function() {
    syncAddressContent();
    const btn = document.getElementById('btnSubmitSettings');
    const txt = document.getElementById('btnSubmitText');
    if (btn) btn.disabled = true;
    if (txt) txt.textContent = 'Saving Settings...';
});

// Except Doctor Tab Helpers
function toggleAllDoctors(check) {
    document.querySelectorAll('.doctor-checkbox').forEach(cb => {
        cb.checked = check;
    });
    updateExcludedBadgeCount();
}

function updateExcludedBadgeCount() {
    const count = document.querySelectorAll('.doctor-checkbox:checked').length;
    const badge = document.getElementById('tabExcludedBadge');
    if (badge) {
        badge.textContent = count;
        badge.style.background = count > 0 ? '#fee2e2' : '#f1f5f9';
        badge.style.color = count > 0 ? '#dc2626' : '#64748b';
    }
}

function filterDoctorRows(keyword) {
    const q = keyword.toLowerCase().trim();
    let visibleCount = 0;
    document.querySelectorAll('.doctor-row').forEach(row => {
        const text = row.dataset.search || '';
        if (!q || text.includes(q)) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    const status = document.getElementById('doctorCountStatus');
    if (status) {
        status.textContent = `Showing ${visibleCount} doctors`;
    }
}
</script>
@endsection

