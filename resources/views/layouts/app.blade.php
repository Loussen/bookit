{{-- layouts/app.blade.php --}}
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Telebook')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://telegram.org/js/telegram-web-app.js"></script>
    <style>
        :root {
            --tg-theme-bg-color: var(--tg-theme-bg-color, #fff);
            --tg-theme-text-color: var(--tg-theme-text-color, #000);
            --tg-theme-hint-color: var(--tg-theme-hint-color, #999);
            --tg-theme-link-color: var(--tg-theme-link-color, #2481cc);
            --tg-theme-button-color: var(--tg-theme-button-color, #2481cc);
            --tg-theme-button-text-color: var(--tg-theme-button-text-color, #fff);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: var(--tg-theme-bg-color);
            color: var(--tg-theme-text-color);
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        .header {
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.8);
            padding: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            font-size: 20px;
            font-weight: 600;
        }

        .hotel-list {
            padding: 16px;
        }

        .hotel-card {
            background: var(--tg-theme-bg-color);
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .hotel-card:active {
            transform: scale(0.98);
        }

        .hotel-image {
            position: relative;
            height: 200px;
            overflow: hidden;
        }

        .hotel-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .hotel-price-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(0, 0, 0, 0.6);
            color: #fff;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 500;
        }

        .hotel-content {
            padding: 16px;
        }

        .hotel-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 4px;
            color: var(--tg-theme-text-color);
        }

        .hotel-location {
            color: var(--tg-theme-hint-color);
            font-size: 14px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .hotel-rating {
            display: flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 8px;
        }

        .rating-stars {
            color: #ffd700;
        }

        .rating-count {
            color: var(--tg-theme-hint-color);
            font-size: 14px;
        }

        .primary-button {
            background: var(--tg-theme-button-color);
            color: var(--tg-theme-button-text-color);
            border: none;
            border-radius: 8px;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: 500;
            width: 100%;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .primary-button:active {
            opacity: 0.8;
        }

        .search-section {
            padding: 16px;
            background: var(--tg-theme-bg-color);
            margin-bottom: 16px;
        }

        .search-input {
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 12px 16px;
            width: 100%;
            margin-bottom: 12px;
            font-size: 16px;
            background: rgba(0, 0, 0, 0.03);
        }

        .filter-tags {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding: 4px 0;
            -webkit-overflow-scrolling: touch;
        }

        .filter-tag {
            background: rgba(0, 0, 0, 0.05);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            white-space: nowrap;
            color: var(--tg-theme-text-color);
        }

        .filter-tag.active {
            background: var(--tg-theme-button-color);
            color: var(--tg-theme-button-text-color);
        }
    </style>
</head>
<body>
@yield('content')
@stack('scripts')
</body>
</html>
