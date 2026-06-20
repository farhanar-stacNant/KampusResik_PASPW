    <style>
        :root {
            --deepsea-dark: #0f172a;
            --deepsea-medium: #1e293b;
            --deepsea-light: #334155;
            --teal-primary: #0d9488;
            --teal-hover: #0f766e;
            --body-bg: #f8fafc;
        }

        body {
            background-color: var(--body-bg);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            overflow-x: hidden;
        }

        .wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }

        /* Content Area */
        #content {
            flex-grow: 1;
            min-height: 100vh;
            transition: all 0.3s;
        }

        /* Topbar Styling */
        .topbar {
            background-color: #fff;
            height: 70px;
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 99;
        }

        .search-box {
            position: relative;
            width: 300px;
        }

        .search-box input {
            background-color: #f1f5f9;
            border: none;
            border-radius: 20px;
            padding: 8px 16px 8px 40px;
            width: 100%;
            font-size: 0.9rem;
            outline: none;
            transition: all 0.3s;
        }

        .search-box input:focus {
            background-color: #e2e8f0;
        }

        .search-box i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        /* Cards Styling */
        .stat-card {
            background: #fff;
            border-radius: 16px;
            border: none;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.02);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05), 0 4px 6px -2px rgba(0,0,0,0.05);
        }

        .card-badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 12px;
        }

        .card-badge.success {
            background-color: #ccfbf1;
            color: #0d9488;
        }

        .card-badge.danger {
            background-color: #fee2e2;
            color: #ef4444;
        }

        /* Weekly Chart Styling */
        .chart-container {
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            height: 100%;
        }

        .bar-chart {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            height: 200px;
            padding-top: 20px;
        }

        .chart-bar-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex-grow: 1;
        }

        .chart-bar {
            width: 24px;
            background-color: #cbd5e1;
            border-radius: 4px 4px 0 0;
            transition: all 0.5s ease;
            position: relative;
        }

        .chart-bar.active {
            background-color: var(--teal-primary);
        }

        .chart-bar:hover {
            background-color: var(--teal-hover);
        }

        .bar-label {
            font-size: 0.75rem;
            color: #94a3b8;
            margin-top: 8px;
        }

        /* Tips Card */
        .tips-card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            height: 100%;
        }

        .tips-banner {
            height: 140px;
            background: linear-gradient(135deg, #0d9488 0%, #0f172a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .tips-banner::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: url('../tips_pengelolaan_banner.png') center/cover no-repeat;
            opacity: 0.4;
        }

        /* Toggle Sidebar Button */
        .toggle-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--deepsea-light);
        }
    </style>