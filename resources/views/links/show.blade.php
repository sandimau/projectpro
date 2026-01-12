<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Links | {{ $linkPage->title }}">
    <title>Links | {{ $linkPage->title }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: {{ $linkPage->background_color }};
            --text-color: {{ $linkPage->text_color }};
            --button-color: {{ $linkPage->button_color }};
            --button-text-color: {{ $linkPage->button_text_color }};
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-color);
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px 80px;
        }

        .container {
            max-width: 680px;
            width: 100%;
        }

        /* Profile Section */
        .profile {
            text-align: center;
            margin-bottom: 24px;
        }

        .profile-image {
            width: 96px;
            height: 96px;
            border-radius: 12px;
            object-fit: contain;
            margin-bottom: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .profile-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        /* Social Links */
        .social-links {
            display: flex;
            justify-content: center;
            gap: 16px;
            margin-bottom: 32px;
            flex-wrap: wrap;
        }

        .social-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 16px;
            min-width: 80px;
            border-radius: 12px;
            background: var(--button-color);
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            text-decoration: none;
        }

        .social-link:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.25);
        }

        .social-link img {
            width: 40px;
            height: 40px;
            object-fit: contain;
            border-radius: 8px;
        }

        .social-link .social-icon {
            font-size: 28px;
            color: var(--button-text-color);
        }

        .social-link .social-title {
            font-size: 12px;
            font-weight: 500;
            color: var(--button-text-color);
            text-align: center;
            max-width: 80px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Section Header */
        .section {
            margin-bottom: 24px;
        }

        .section-title {
            font-size: 24px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 16px;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Link Buttons */
        .links {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .link-button {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            background: var(--button-color);
            color: var(--button-text-color);
            padding: 8px 12px 8px 8px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 500;
            font-size: 18px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
            height: 70px;
        }

        .link-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }

        .link-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: 0.5s;
        }

        .link-button:hover::before {
            left: 100%;
        }

        .link-button img {
            width: 50px;
            height: 100%;
            border-radius: 6px;
            object-fit: cover;
            background-color: var(--bg-color);
            padding: 2px;
        }

        .link-button .link-text {
            flex: 1;
            text-align: center;
        }

        .link-button .external-icon {
            font-size: 14px;
        }

        /* Footer */
        .footer {
            margin-top: auto;
            padding-top: 40px;
            text-align: center;
            opacity: 0.6;
            font-size: 12px;
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .profile {
            animation: fadeInUp 0.6s ease-out;
        }

        .social-links {
            animation: fadeInUp 0.6s ease-out 0.1s both;
        }

        .section {
            animation: fadeInUp 0.6s ease-out 0.2s both;
        }

        .link-button {
            animation: fadeInUp 0.5s ease-out both;
        }

        .links .link-button:nth-child(1) { animation-delay: 0.3s; }
        .links .link-button:nth-child(2) { animation-delay: 0.35s; }
        .links .link-button:nth-child(3) { animation-delay: 0.4s; }
        .links .link-button:nth-child(4) { animation-delay: 0.45s; }
        .links .link-button:nth-child(5) { animation-delay: 0.5s; }
        .links .link-button:nth-child(6) { animation-delay: 0.55s; }

        /* Responsive */
        @media (max-width: 480px) {
            body {
                padding: 24px 16px 60px;
            }

            .profile-image {
                width: 80px;
                height: 80px;
            }

            .profile-title {
                font-size: 18px;
            }

            .link-button {
                padding: 12px 16px;
                font-size: 14px;
            }
        }

        /* SVG Icons */
        .svg-icon {
            width: 28px;
            height: 28px;
            fill: var(--button-text-color);
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Profile Section -->
        <div class="profile">
            @if($linkPage->logo)
                <img src="{{ asset('uploads/links/' . $linkPage->logo) }}" alt="{{ $linkPage->title }}" class="profile-image">
            @else
                <div class="profile-image" style="background: var(--button-color); display: flex; align-items: center; justify-content: center; border-radius: 12px;">
                    <span style="font-size: 36px; color: var(--button-text-color);">{{ substr($linkPage->title, 0, 1) }}</span>
                </div>
            @endif
            <h1 class="profile-title">{{ $linkPage->title }}</h1>
        </div>

        <!-- Social Links -->
        @if($socialLinks->count() > 0)
        <div class="social-links">
            @foreach($socialLinks as $social)
            <a href="{{ $social->url }}" target="_blank" rel="noopener noreferrer" class="social-link">
                @if($social->icon)
                    <img src="{{ asset('uploads/links/icons/' . $social->icon) }}" alt="{{ $social->title }}">
                @else
                    @php
                        $iconName = strtolower($social->title);
                    @endphp
                    @if(str_contains($iconName, 'youtube'))
                        <svg class="svg-icon" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    @elseif(str_contains($iconName, 'instagram'))
                        <svg class="svg-icon" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/></svg>
                    @elseif(str_contains($iconName, 'tiktok'))
                        <svg class="svg-icon" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                    @elseif(str_contains($iconName, 'facebook'))
                        <svg class="svg-icon" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    @elseif(str_contains($iconName, 'twitter') || str_contains($iconName, 'x'))
                        <svg class="svg-icon" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    @elseif(str_contains($iconName, 'whatsapp'))
                        <svg class="svg-icon" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    @else
                        <svg class="svg-icon" viewBox="0 0 24 24"><path d="M14.851 11.923c-.179-.641-.521-1.246-1.025-1.749-1.562-1.562-4.095-1.563-5.657 0l-4.998 4.998c-1.562 1.563-1.563 4.095 0 5.657 1.562 1.563 4.096 1.561 5.656 0l3.842-3.841.333.009c.404 0 .802-.04 1.189-.117l-4.657 4.656c-.975.976-2.255 1.464-3.535 1.464-1.28 0-2.56-.488-3.535-1.464-1.952-1.951-1.952-5.12 0-7.071l4.998-4.998c.975-.976 2.256-1.464 3.536-1.464 1.279 0 2.56.488 3.535 1.464.493.493.861 1.063 1.105 1.672l-.787.784zm-5.703.147c.178.643.521 1.25 1.026 1.756 1.562 1.563 4.096 1.561 5.656 0l4.999-4.998c1.563-1.562 1.563-4.095 0-5.657-1.562-1.562-4.095-1.563-5.657 0l-3.841 3.841-.333-.009c-.404 0-.802.04-1.189.117l4.656-4.656c.975-.976 2.256-1.464 3.536-1.464 1.279 0 2.56.488 3.535 1.464 1.951 1.951 1.951 5.119 0 7.071l-4.999 4.998c-.975.976-2.255 1.464-3.535 1.464-1.28 0-2.56-.488-3.535-1.464-.494-.495-.863-1.067-1.107-1.678l.788-.785z"/></svg>
                    @endif
                @endif
                <span class="social-title">{{ $social->title }}</span>
            </a>
            @endforeach
        </div>
        @endif

        <!-- Link Sections -->
        @foreach($sections as $sectionName => $items)
        <div class="section">
            @if($sectionName)
                <h2 class="section-title">{{ $sectionName }}</h2>
            @endif
            <div class="links">
                @foreach($items as $item)
                <a href="{{ $item->url }}" target="_blank" rel="noopener noreferrer" class="link-button">
                    @if($item->icon)
                        <img class="link-icon" src="{{ asset('uploads/links/icons/' . $item->icon) }}" alt="{{ $item->title }}">
                    @endif
                    <span class="link-text">{{ $item->title }}</span>
                    <span class="external-icon">↗</span>
                </a>
                @endforeach
            </div>
        </div>
        @endforeach

        @if($sections->isEmpty() && $socialLinks->isEmpty())
        <div style="text-align: center; padding: 40px 20px; opacity: 0.7;">
            <p>Belum ada link yang ditambahkan.</p>
        </div>
        @endif
    </div>
</body>
</html>

