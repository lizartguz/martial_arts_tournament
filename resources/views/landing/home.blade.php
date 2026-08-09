@php
    $contactPhoneDisplay = '+591 60000005';
    $contactPhoneHref = 'tel:+59160000005';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ __('messages.landing.home.meta.description') }}">
    <title>{{ __('messages.landing.home.meta.title') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('frontend/images/logo_with_text.png') }}?v=20260510">
    <link rel="shortcut icon" type="image/png" href="{{ asset('frontend/images/logo_with_text.png') }}?v=20260510">
    <style>
        :root {
            --green: #16a34a;
            --green-dark: #0f7a3a;
            --blue: #1d4ed8;
            --ink: #0f172a;
            --muted: #64748b;
            --soft: #f5f8f6;
            --line: rgba(15, 23, 42, 0.12);
            --white: #ffffff;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--ink);
            background: var(--white);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        img {
            display: block;
            max-width: 100%;
        }

        .page-shell {
            overflow: hidden;
            background: #fff;
        }

        .site-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
            border-bottom: 1px solid rgba(255, 255, 255, 0.16);
            background: rgba(3, 7, 18, 0.68);
            backdrop-filter: blur(18px);
        }

        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            width: min(1180px, calc(100% - 32px));
            height: 76px;
            margin: 0 auto;
            color: #fff;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            letter-spacing: 0.02em;
        }

        .brand-logo {
            width: 44px;
            height: 44px;
            object-fit: contain;
        }

        .brand-name {
            font-size: 18px;
            font-weight: 900;
            letter-spacing: 0.12em;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 22px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .nav-links a {
            color: rgba(255, 255, 255, 0.82);
            transition: color 160ms ease;
        }

        .nav-links a:hover {
            color: #fff;
        }

        .nav-cta {
            border: 1px solid rgba(255, 255, 255, 0.35);
            padding: 11px 16px;
        }

        .nav-cta:hover {
            background: #fff;
            color: var(--ink) !important;
        }

        .menu-toggle {
            display: none;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border: 1px solid rgba(255, 255, 255, 0.24);
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            transition: background 160ms ease, border-color 160ms ease;
        }

        .menu-toggle:hover {
            background: rgba(255, 255, 255, 0.16);
            border-color: rgba(255, 255, 255, 0.42);
        }

        .menu-toggle svg {
            width: 22px;
            height: 22px;
            stroke: currentColor;
        }

        .mobile-menu {
            display: none;
        }

        .hero {
            position: relative;
            min-height: 100vh;
            padding-top: 76px;
            color: #fff;
            background: #030712;
        }

        .hero-bg,
        .cta-bg {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .hero::before {
            position: absolute;
            inset: 0;
            content: "";
            background:
                linear-gradient(90deg, rgba(2, 6, 23, 0.95), rgba(2, 6, 23, 0.78) 45%, rgba(15, 118, 110, 0.22)),
                linear-gradient(0deg, rgba(2, 6, 23, 0.2), rgba(2, 6, 23, 0.05));
            z-index: 1;
        }

        .hero-inner {
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: minmax(0, 1.25fr) minmax(300px, 0.75fr);
            align-items: center;
            gap: 54px;
            width: min(1180px, calc(100% - 32px));
            min-height: calc(100vh - 76px);
            margin: 0 auto;
            padding: 68px 0;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            border: 1px solid rgba(110, 231, 183, 0.42);
            background: rgba(16, 185, 129, 0.14);
            padding: 10px 14px;
            color: #d1fae5;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.16em;
            text-transform: uppercase;
        }

        h1,
        h2,
        h3,
        p {
            margin: 0;
        }

        h1 {
            max-width: 760px;
            margin-top: 22px;
            font-size: clamp(42px, 6vw, 78px);
            line-height: 0.98;
            letter-spacing: 0;
        }

        .hero-copy {
            max-width: 650px;
            margin-top: 24px;
            color: rgba(255, 255, 255, 0.82);
            font-size: clamp(16px, 1.8vw, 20px);
            line-height: 1.75;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 34px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 48px;
            padding: 0 22px;
            border: 1px solid transparent;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            transition: transform 160ms ease, background 160ms ease, border-color 160ms ease;
        }

        .button:hover {
            transform: translateY(-1px);
        }

        .button-primary {
            background: var(--green);
            color: #fff;
        }

        .button-primary:hover {
            background: #22c55e;
        }

        .button-light {
            border-color: rgba(255, 255, 255, 0.5);
            color: #fff;
        }

        .button-light:hover {
            background: #fff;
            color: var(--ink);
        }

        .button-dark {
            background: var(--ink);
            color: #fff;
        }

        .button-outline {
            border-color: var(--line);
            color: var(--ink);
        }

        .button-outline:hover {
            border-color: var(--ink);
            background: var(--ink);
            color: #fff;
        }

        .solution-picker {
            margin-top: 28px;
        }

        .solution-picker input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .solution-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .solution-tabs label {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            border: 1px solid var(--line);
            border-radius: 999px;
            padding: 0 18px;
            color: var(--ink);
            font-size: 13px;
            font-weight: 900;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            cursor: pointer;
            transition: background 160ms ease, border-color 160ms ease, color 160ms ease, transform 160ms ease;
        }

        .solution-tabs label:hover {
            transform: translateY(-1px);
            border-color: var(--green);
        }

        #solution-agro:checked ~ .solution-tabs label[for="solution-agro"],
        #solution-civil:checked ~ .solution-tabs label[for="solution-civil"],
        #solution-sensors:checked ~ .solution-tabs label[for="solution-sensors"],
        #solution-alerts:checked ~ .solution-tabs label[for="solution-alerts"] {
            border-color: var(--green);
            background: var(--green);
            color: #fff;
        }

        .solution-panels {
            margin-top: 18px;
        }

        .solution-panel {
            display: none;
            border: 1px solid var(--line);
            border-radius: 28px;
            background: #fff;
            padding: 24px;
            box-shadow: 0 18px 55px rgba(15, 23, 42, 0.08);
        }

        .solution-panel h3 {
            font-size: 24px;
        }

        .solution-panel p {
            margin-top: 10px;
            color: var(--muted);
            line-height: 1.75;
        }

        .solution-list {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            margin-top: 18px;
        }

        .solution-list span {
            border-radius: 999px;
            background: var(--soft);
            padding: 10px 12px;
            color: var(--green-dark);
            font-size: 13px;
            font-weight: 800;
            text-align: center;
        }

        #solution-agro:checked ~ .solution-panels .panel-agro,
        #solution-civil:checked ~ .solution-panels .panel-civil,
        #solution-sensors:checked ~ .solution-panels .panel-sensors,
        #solution-alerts:checked ~ .solution-panels .panel-alerts {
            display: block;
        }

        .signal-card {
            align-self: end;
        }

        .signal-list {
            display: grid;
            gap: 16px;
        }

        .signal-item {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 16px;
            align-items: start;
            border: 1px solid rgba(148, 163, 184, 0.22);
            border-radius: 24px;
            background:
                linear-gradient(180deg, rgba(15, 23, 42, 0.92), rgba(2, 6, 23, 0.9));
            padding: 18px 20px;
            backdrop-filter: blur(14px);
            box-shadow:
                0 20px 40px rgba(2, 6, 23, 0.36),
                inset 0 1px 0 rgba(255, 255, 255, 0.06);
            transition: transform 180ms ease, box-shadow 180ms ease, border-color 180ms ease;
        }

        .signal-item:nth-child(2) {
            transform: translateX(28px);
        }

        .signal-item:nth-child(3) {
            transform: translateX(10px);
        }

        .signal-item:hover {
            transform: translateY(-4px);
            border-color: rgba(134, 239, 172, 0.32);
            box-shadow:
                0 24px 46px rgba(2, 6, 23, 0.42),
                inset 0 1px 0 rgba(255, 255, 255, 0.08);
        }

        .signal-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.24), rgba(22, 163, 74, 0.1));
            color: #bbf7d0;
            font-size: 18px;
            font-weight: 900;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
        }

        .signal-item:nth-child(2) .signal-icon {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.24), rgba(29, 78, 216, 0.1));
            color: #bfdbfe;
        }

        .signal-item:nth-child(3) .signal-icon {
            background: linear-gradient(135deg, rgba(251, 191, 36, 0.24), rgba(245, 158, 11, 0.1));
            color: #fde68a;
        }

        .signal-copy {
            min-width: 0;
        }

        .signal-value {
            font-size: 22px;
            font-weight: 900;
            line-height: 1.05;
            letter-spacing: -0.03em;
            color: #f8fafc;
        }

        .signal-text {
            margin-top: 6px;
            color: rgba(226, 232, 240, 0.78);
            font-size: 14px;
            line-height: 1.6;
        }

        .section {
            padding: 92px 0;
        }

        .section-soft {
            background: var(--soft);
        }

        .container {
            width: min(1180px, calc(100% - 32px));
            margin: 0 auto;
        }

        .split {
            display: grid;
            grid-template-columns: minmax(0, 0.95fr) minmax(0, 1.05fr);
            align-items: center;
            gap: 56px;
        }

        .section-kicker {
            color: var(--green-dark);
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 0.16em;
            text-transform: uppercase;
        }

        .section-title {
            margin-top: 12px;
            font-size: clamp(32px, 4vw, 50px);
            line-height: 1.06;
            letter-spacing: 0;
        }

        .section-text {
            margin-top: 18px;
            color: var(--muted);
            font-size: 17px;
            line-height: 1.8;
        }

        .media-frame {
            overflow: hidden;
            min-height: 430px;
            border-radius: 34px;
            background: #020617;
            box-shadow: 0 24px 70px rgba(15, 23, 42, 0.18);
        }

        .media-frame img {
            width: 100%;
            height: 100%;
            min-height: 430px;
            object-fit: cover;
        }

        .dark-section {
            background: #020617;
            color: #fff;
        }

        .service-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            margin-top: 42px;
        }

        .service-card {
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.055);
            padding: 26px;
        }

        .service-card h3 {
            color: #86efac;
            font-size: 20px;
        }

        .service-card p {
            margin-top: 12px;
            color: rgba(255, 255, 255, 0.72);
            font-size: 14px;
            line-height: 1.75;
        }

        .benefits {
            display: grid;
            gap: 20px;
            margin-top: 28px;
        }

        .benefit {
            display: flex;
            gap: 16px;
        }

        .dot {
            flex: 0 0 auto;
            width: 12px;
            height: 12px;
            margin-top: 7px;
            background: var(--green);
        }

        .benefit:nth-child(2) .dot {
            background: var(--blue);
        }

        .benefit:nth-child(3) .dot {
            background: #f59e0b;
        }

        .benefit p {
            margin-top: 5px;
            color: var(--muted);
            font-size: 15px;
            line-height: 1.7;
        }

        .photo-pair {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .photo-pair img {
            width: 100%;
            height: 360px;
            border-radius: 36px;
            object-fit: cover;
        }

        .photo-pair img:nth-child(2) {
            margin-top: 44px;
            border-radius: 999px;
        }

        .mini-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .mini-card {
            border-radius: 24px;
            background: #fff;
            padding: 24px;
            box-shadow: 0 16px 44px rgba(15, 23, 42, 0.08);
        }

        .mini-card img {
            width: 100%;
            height: 160px;
            border-radius: 22px;
            object-fit: cover;
            margin-bottom: 20px;
            background: #eef2f7;
        }

        .mini-card h3 {
            font-size: 22px;
        }

        .mini-card p {
            margin-top: 10px;
            color: var(--muted);
            font-size: 15px;
            line-height: 1.7;
        }

        .final-cta {
            position: relative;
            color: #fff;
            background: #020617;
        }

        .final-cta::before {
            position: absolute;
            inset: 0;
            content: "";
            background: linear-gradient(90deg, rgba(2, 6, 23, 0.94), rgba(2, 6, 23, 0.72), rgba(22, 163, 74, 0.2));
            z-index: 1;
        }

        .final-cta .container {
            position: relative;
            z-index: 2;
            max-width: 880px;
            text-align: center;
        }

        .site-footer {
            background: #020617;
            color: #dbeafe;
        }

        .footer-inner {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(260px, 0.7fr);
            align-items: center;
            gap: 28px;
            width: min(1180px, calc(100% - 32px));
            margin: 0 auto;
            padding: 24px 0;
        }

        .footer-logo-row {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .footer-logo-row img {
            width: 46px;
            height: 46px;
            object-fit: contain;
        }

        .footer-logo-row span {
            color: #fff;
            font-size: 18px;
            font-weight: 900;
            letter-spacing: 0.12em;
        }

        .footer-brand p,
        .footer-contact p {
            margin-top: 8px;
            color: rgba(219, 234, 254, 0.72);
            font-size: 14px;
            line-height: 1.55;
        }

        .footer-contact {
            display: grid;
            gap: 7px;
        }

        .footer-contact a {
            width: fit-content;
            color: #fff;
            font-weight: 800;
        }

        .social-icons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 12px;
        }

        .social-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            transition: transform 160ms ease, background 160ms ease;
        }

        .social-icon:hover {
            transform: translateY(-2px);
            background: var(--green);
        }

        .social-icon svg {
            width: 19px;
            height: 19px;
            fill: currentColor;
        }

        .footer-note {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 12px 16px;
            color: rgba(219, 234, 254, 0.62);
            font-size: 13px;
            text-align: center;
        }

        @media (max-width: 920px) {
            .nav {
                flex-wrap: wrap;
                width: min(1180px, calc(100% - 24px));
                height: auto;
                padding: 14px 0;
            }

            .nav-links {
                display: none;
            }

            .menu-toggle {
                display: inline-flex;
            }

            .mobile-menu {
                width: 100%;
                margin-top: 10px;
                border: 1px solid rgba(255, 255, 255, 0.12);
                border-radius: 22px;
                background: rgba(2, 6, 23, 0.92);
                padding: 10px;
                box-shadow: 0 22px 44px rgba(2, 6, 23, 0.28);
            }

            .mobile-menu.is-open {
                display: grid;
                gap: 8px;
            }

            .mobile-menu a {
                display: block;
                border-radius: 16px;
                padding: 13px 14px;
                color: rgba(255, 255, 255, 0.9);
                font-size: 13px;
                font-weight: 800;
                letter-spacing: 0.08em;
                text-transform: uppercase;
            }

            .mobile-menu a:hover {
                background: rgba(255, 255, 255, 0.08);
                color: #fff;
            }

            .mobile-menu .nav-cta {
                border-color: rgba(255, 255, 255, 0.18);
                text-align: center;
            }

            .hero-inner,
            .split,
            .footer-inner {
                grid-template-columns: 1fr;
            }

            .hero-inner {
                gap: 30px;
                min-height: auto;
                padding: 48px 0 56px;
            }

            .signal-card {
                align-self: auto;
            }

            .service-grid,
            .mini-grid,
            .solution-list {
                grid-template-columns: 1fr;
            }

            .photo-pair img {
                height: 280px;
            }
        }

        @media (max-width: 640px) {
            .brand {
                gap: 8px;
            }

            .brand-logo {
                width: 40px;
                height: 40px;
            }

            .brand-name {
                font-size: 15px;
                letter-spacing: 0.08em;
            }

            .section {
                padding: 70px 0;
            }

            .actions,
            .socials {
                grid-template-columns: 1fr;
            }

            .button {
                width: 100%;
            }

            .solution-panel,
            .service-card,
            .mini-card {
                padding: 20px;
            }

            .signal-item {
                grid-template-columns: 1fr;
                transform: none;
            }

            .photo-pair {
                grid-template-columns: 1fr;
            }

            .photo-pair img:nth-child(2) {
                margin-top: 0;
            }

            .contact-card {
                padding: 22px;
            }

            .phone {
                font-size: 25px;
            }
        }

        @media (max-width: 420px) {
            .brand-name {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="page-shell">
        <header class="site-header">
            <nav class="nav" aria-label="Navegacion principal">
                <a class="brand" href="{{ route('landing.home') }}" aria-label="SenvaTec inicio">
                    <img class="brand-logo" src="{{ asset('frontend/images/logo_with_text.png') }}" alt="SenvaTec">
                    <span class="brand-name">SENVATEC</span>
                </a>
                <button
                    class="menu-toggle"
                    type="button"
                    aria-expanded="false"
                    aria-controls="mobile-menu"
                    aria-label="Abrir menu de navegacion">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M4 7h16"></path>
                        <path d="M4 12h16"></path>
                        <path d="M4 17h16"></path>
                    </svg>
                </button>
                <div class="nav-links">
                    <a href="#soluciones">{{ __('messages.landing.home.nav.solutions') }}</a>
                    <a href="#beneficios">{{ __('messages.landing.home.nav.benefits') }}</a>
                    <a href="#footer-contacto">{{ __('messages.landing.home.nav.contact') }}</a>
                    <a href="{{ route('login') }}">{{ __('messages.landing.home.nav.login') }}</a>
                    <a class="nav-cta" href="{{ $contactPhoneHref }}">{{ $contactPhoneDisplay }}</a>
                    <x-locale-switcher />
                </div>
                <div id="mobile-menu" class="mobile-menu" hidden>
                    <a href="#soluciones">{{ __('messages.landing.home.nav.solutions') }}</a>
                    <a href="#beneficios">{{ __('messages.landing.home.nav.benefits') }}</a>
                    <a href="#footer-contacto">{{ __('messages.landing.home.nav.contact') }}</a>
                    <a href="{{ route('login') }}">{{ __('messages.landing.home.nav.login') }}</a>
                    <a class="nav-cta" href="{{ $contactPhoneHref }}">{{ $contactPhoneDisplay }}</a>
                    <div style="padding: 8px 4px 2px;"><x-locale-switcher /></div>
                </div>
            </nav>
        </header>

        <main>
            <section class="hero">
                <img class="hero-bg" src="{{ asset('frontend/images/senvatec-hero-weather-station.png') }}" alt="Estacion agrometeorologica moderna en un cultivo">
                <div class="hero-inner">
                    <div>
                        <p class="eyebrow">{{ __('messages.landing.home.hero.eyebrow') }}</p>
                        <h1>{{ __('messages.landing.home.hero.title') }}</h1>
                        <p class="hero-copy">
                            {{ __('messages.landing.home.hero.copy') }}
                        </p>
                        <div class="actions">
                            <a class="button button-primary" href="{{ $contactPhoneHref }}">{{ __('messages.landing.home.hero.call') }}</a>
                            <a class="button button-light" href="#footer-contacto">{{ __('messages.landing.home.hero.request') }}</a>
                        </div>
                    </div>

                    <div class="signal-card" aria-label="{{ __('messages.landing.home.hero.eyebrow') }}">
                        <div class="signal-list">
                            <div class="signal-item">
                                <span class="signal-icon">24</span>
                                <div class="signal-copy">
                                    <p class="signal-value">24/7</p>
                                    <p class="signal-text">{{ __('messages.landing.home.signals.monitoring_text') }}</p>
                                </div>
                            </div>
                            <div class="signal-item">
                                <span class="signal-icon">Ag</span>
                                <div class="signal-copy">
                                    <p class="signal-value">{{ __('messages.landing.home.signals.agro_value') }}</p>
                                    <p class="signal-text">{{ __('messages.landing.home.signals.agro_text') }}</p>
                                </div>
                            </div>
                            <div class="signal-item">
                                <span class="signal-icon">SL</span>
                                <div class="signal-copy">
                                    <p class="signal-value">{{ __('messages.landing.home.signals.support_value') }}</p>
                                    <p class="signal-text">{{ __('messages.landing.home.signals.support_text') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="soluciones" class="section">
                <div class="container split">
                    <div>
                        <p class="section-kicker">{{ __('messages.landing.home.solutions.kicker') }}</p>
                        <h2 class="section-title">{{ __('messages.landing.home.solutions.title') }}</h2>
                        <p class="section-text">
                            {{ __('messages.landing.home.solutions.text') }}
                        </p>
                        <div class="solution-picker" aria-label="{{ __('messages.landing.home.solutions.kicker') }}">
                            <input type="radio" id="solution-agro" name="solution-option" checked>
                            <input type="radio" id="solution-civil" name="solution-option">
                            <input type="radio" id="solution-sensors" name="solution-option">
                            <input type="radio" id="solution-alerts" name="solution-option">

                            <div class="solution-tabs">
                                <label for="solution-agro">{{ __('messages.landing.home.solutions.tab_agro') }}</label>
                                <label for="solution-civil">{{ __('messages.landing.home.solutions.tab_civil') }}</label>
                                <label for="solution-sensors">{{ __('messages.landing.home.solutions.tab_sensors') }}</label>
                                <label for="solution-alerts">{{ __('messages.landing.home.solutions.tab_alerts') }}</label>
                            </div>

                            <div class="solution-panels">
                                <article class="solution-panel panel-agro">
                                    <h3>{{ __('messages.landing.home.solutions.agro_title') }}</h3>
                                    <p>
                                        {{ __('messages.landing.home.solutions.agro_text') }}
                                    </p>
                                    <div class="solution-list">
                                        <span>{{ __('messages.landing.home.solutions.agro_tag_1') }}</span>
                                        <span>{{ __('messages.landing.home.solutions.agro_tag_2') }}</span>
                                        <span>{{ __('messages.landing.home.solutions.agro_tag_3') }}</span>
                                    </div>
                                </article>
                                <article class="solution-panel panel-civil">
                                    <h3>{{ __('messages.landing.home.solutions.civil_title') }}</h3>
                                    <p>
                                        {{ __('messages.landing.home.solutions.civil_text') }}
                                    </p>
                                    <div class="solution-list">
                                        <span>{{ __('messages.landing.home.solutions.civil_tag_1') }}</span>
                                        <span>{{ __('messages.landing.home.solutions.civil_tag_2') }}</span>
                                        <span>{{ __('messages.landing.home.solutions.civil_tag_3') }}</span>
                                    </div>
                                </article>
                                <article class="solution-panel panel-sensors">
                                    <h3>{{ __('messages.landing.home.solutions.sensors_title') }}</h3>
                                    <p>
                                        {{ __('messages.landing.home.solutions.sensors_text') }}
                                    </p>
                                    <div class="solution-list">
                                        <span>{{ __('messages.landing.home.solutions.sensors_tag_1') }}</span>
                                        <span>{{ __('messages.landing.home.solutions.sensors_tag_2') }}</span>
                                        <span>{{ __('messages.landing.home.solutions.sensors_tag_3') }}</span>
                                    </div>
                                </article>
                                <article class="solution-panel panel-alerts">
                                    <h3>{{ __('messages.landing.home.solutions.alerts_title') }}</h3>
                                    <p>
                                        {{ __('messages.landing.home.solutions.alerts_text') }}
                                    </p>
                                    <div class="solution-list">
                                        <span>{{ __('messages.landing.home.solutions.alerts_tag_1') }}</span>
                                        <span>{{ __('messages.landing.home.solutions.alerts_tag_2') }}</span>
                                        <span>{{ __('messages.landing.home.solutions.alerts_tag_3') }}</span>
                                    </div>
                                </article>
                            </div>
                        </div>
                    </div>
                    <div class="media-frame">
                        <img src="{{ asset('frontend/images/senvatec-monitoring-dashboard.png') }}" alt="Panel de monitoreo meteorologico y sensores">
                    </div>
                </div>
            </section>

            <section class="section dark-section">
                <div class="container">
                    <p class="section-kicker">{{ __('messages.landing.home.services.kicker') }}</p>
                    <h2 class="section-title">{{ __('messages.landing.home.services.title') }}</h2>
                    <div class="service-grid">
                        <article class="service-card">
                            <h3>{{ __('messages.landing.home.services.stations_title') }}</h3>
                            <p>{{ __('messages.landing.home.services.stations_text') }}</p>
                        </article>
                        <article class="service-card">
                            <h3>{{ __('messages.landing.home.services.sensors_title') }}</h3>
                            <p>{{ __('messages.landing.home.services.sensors_text') }}</p>
                        </article>
                        <article class="service-card">
                            <h3>{{ __('messages.landing.home.services.monitoring_title') }}</h3>
                            <p>{{ __('messages.landing.home.services.monitoring_text') }}</p>
                        </article>
                        <article class="service-card">
                            <h3>{{ __('messages.landing.home.services.alerts_title') }}</h3>
                            <p>{{ __('messages.landing.home.services.alerts_text') }}</p>
                        </article>
                    </div>
                </div>
            </section>

            <section id="beneficios" class="section">
                <div class="container split">
                    <div class="photo-pair">
                        <img src="{{ asset('frontend/images/senvatec-agro-monitoring.png') }}" alt="Sensores agrometeorologicos en cultivo">
                        <img src="{{ asset('frontend/images/senvatec-sensor-network.png') }}" alt="Red moderna de sensores meteorologicos">
                    </div>
                    <div>
                        <p class="section-kicker">{{ __('messages.landing.home.benefits.kicker') }}</p>
                        <h2 class="section-title">{{ __('messages.landing.home.benefits.title') }}</h2>
                        <div class="benefits">
                            <div class="benefit">
                                <span class="dot"></span>
                                <div>
                                    <h3>{{ __('messages.landing.home.benefits.b1_title') }}</h3>
                                    <p>{{ __('messages.landing.home.benefits.b1_text') }}</p>
                                </div>
                            </div>
                            <div class="benefit">
                                <span class="dot"></span>
                                <div>
                                    <h3>{{ __('messages.landing.home.benefits.b2_title') }}</h3>
                                    <p>{{ __('messages.landing.home.benefits.b2_text') }}</p>
                                </div>
                            </div>
                            <div class="benefit">
                                <span class="dot"></span>
                                <div>
                                    <h3>{{ __('messages.landing.home.benefits.b3_title') }}</h3>
                                    <p>{{ __('messages.landing.home.benefits.b3_text') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="section section-soft">
                <div class="container mini-grid">
                    <article class="mini-card">
                        <img src="{{ asset('frontend/images/senvatec-sensor-network.png') }}" alt="Red de sensores">
                        <h3>{{ __('messages.landing.home.mini.network_title') }}</h3>
                        <p>{{ __('messages.landing.home.mini.network_text') }}</p>
                    </article>
                    <article class="mini-card">
                        <img src="{{ asset('frontend/images/senvatec-agro-monitoring.png') }}" alt="Aplicacion agrometeorologica">
                        <h3>{{ __('messages.landing.home.mini.agro_title') }}</h3>
                        <p>{{ __('messages.landing.home.mini.agro_text') }}</p>
                    </article>
                    <article class="mini-card">
                        <img src="{{ asset('frontend/images/senvatec-meteorology-operations.png') }}" alt="Monitoreo meteorologico civil">
                        <h3>{{ __('messages.landing.home.mini.applied_title') }}</h3>
                        <p>{{ __('messages.landing.home.mini.applied_text') }}</p>
                    </article>
                </div>
            </section>

            <section id="contacto" class="section final-cta">
                <img class="cta-bg" src="{{ asset('frontend/images/senvatec-cta-landscape.png') }}" alt="Paisaje agricola monitoreado">
                <div class="container">
                    <div>
                        <p class="eyebrow">{{ __('messages.landing.home.cta.eyebrow') }}</p>
                        <h2 class="section-title">{{ __('messages.landing.home.cta.title') }}</h2>
                        <p class="section-text" style="color: rgba(255,255,255,0.78);">
                            {{ __('messages.landing.home.cta.text') }}
                        </p>
                        <div class="actions" style="justify-content: center;">
                            <a class="button button-primary" href="#footer-contacto">{{ __('messages.landing.home.cta.see_contact') }}</a>
                            <a class="button button-light" href="{{ route('login') }}">{{ __('messages.landing.home.cta.login') }}</a>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer id="footer-contacto" class="site-footer">
            <div class="footer-inner">
                <div class="footer-brand">
                    <div class="footer-logo-row">
                        <img src="{{ asset('frontend/images/logo_with_text.png') }}" alt="SenvaTec">
                        <span>SENVATEC</span>
                    </div>
                    <p>
                        {{ __('messages.landing.home.footer.brand_text') }}
                    </p>
                    <div class="social-icons" aria-label="Redes sociales">
                        <a class="social-icon" href="https://www.facebook.com/artguz.bolivia" target="_blank" rel="noopener" aria-label="Facebook">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 8.5V6.8c0-.8.2-1.3 1.4-1.3H17V2.2c-.8-.1-1.7-.2-2.5-.2-2.6 0-4.4 1.6-4.4 4.5v2H7v3.7h3.1V22H14v-9.8h2.9l.5-3.7H14Z"/></svg>
                        </a>
                        <a class="social-icon" href="#" aria-label="TikTok">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16.8 2c.3 2.3 1.6 3.9 3.7 4.1v3.5c-1.2.1-2.4-.3-3.6-1v6.6c0 4.4-4.8 7.2-8.6 5-3.6-2.1-3.8-7.4-.4-9.8 1-.7 2.3-1.1 3.5-1v3.7c-.6-.1-1.2 0-1.8.4-1.7 1.1-1.3 3.8.7 4.3 1.3.3 2.8-.7 2.8-2.3V2h3.7Z"/></svg>
                        </a>
                        <a class="social-icon" href="#" aria-label="YouTube">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21.6 7.2s-.2-1.5-.8-2.1c-.8-.8-1.7-.8-2.1-.9C15.8 4 12 4 12 4s-3.8 0-6.7.2c-.4.1-1.3.1-2.1.9-.6.6-.8 2.1-.8 2.1S2 9 2 10.9v1.7c0 1.8.3 3.7.3 3.7s.2 1.5.8 2.1c.8.8 1.9.8 2.4.9 1.7.2 6.5.2 6.5.2s3.8 0 6.7-.3c.4 0 1.3-.1 2.1-.9.6-.6.8-2.1.8-2.1s.4-1.8.4-3.7v-1.7c0-1.8-.4-3.6-.4-3.6ZM10 14.8V8.6l5.8 3.1L10 14.8Z"/></svg>
                        </a>
                    </div>
                </div>
                <div class="footer-contact">
                    <p class="section-kicker">{{ __('messages.landing.home.footer.contact') }}</p>
                    <a href="{{ $contactPhoneHref }}">{{ $contactPhoneDisplay }}</a>
                    <a href="https://artguz.com" target="_blank" rel="noopener">artguz.com</a>
                    <p>{{ __('messages.landing.home.footer.address') }}</p>
                    <a class="button button-primary" href="{{ route('landing.contact') }}">{{ __('messages.landing.home.footer.contact_us') }}</a>
                </div>
            </div>
            <div class="footer-note">
                {{ __('messages.landing.home.footer.note') }}
            </div>
        </footer>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var menuToggle = document.querySelector('.menu-toggle');
            var mobileMenu = document.getElementById('mobile-menu');

            if (!menuToggle || !mobileMenu) {
                return;
            }

            var closeMenu = function () {
                menuToggle.setAttribute('aria-expanded', 'false');
                mobileMenu.hidden = true;
                mobileMenu.classList.remove('is-open');
            };

            menuToggle.addEventListener('click', function () {
                var isOpen = menuToggle.getAttribute('aria-expanded') === 'true';

                if (isOpen) {
                    closeMenu();
                    return;
                }

                menuToggle.setAttribute('aria-expanded', 'true');
                mobileMenu.hidden = false;
                mobileMenu.classList.add('is-open');
            });

            mobileMenu.querySelectorAll('a').forEach(function (link) {
                link.addEventListener('click', closeMenu);
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth > 920) {
                    closeMenu();
                }
            });
        });
    </script>
</body>
</html>
