<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Announcement - LincHostel</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            margin: 20px auto;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #cc0000, #a30000);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            letter-spacing: 1px;
        }
        .content {
            padding: 30px;
        }
        .announcement-title {
            color: #cc0000;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }
        .announcement-body {
            font-size: 16px;
            color: #555;
            white-space: pre-wrap;
        }
        .footer {
            background-color: #f9f9f9;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #888;
            border-top: 1px solid #eee;
        }
        .btn {
            display: inline-block;
            padding: 12px 25px;
            background-color: #cc0000;
            color: white !important;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 25px;
        }
        .meta-info {
            font-size: 13px;
            color: #999;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>LincHostel</h1>
            <p style="margin: 5px 0 0 0; opacity: 0.8;">Student Portal Announcement</p>
        </div>

        <div class="content">
            <p>Dear Student,</p>
            <p>A new announcement has been posted on the LincHostel portal that may be relevant to you.</p>

            <div class="announcement-title">
                {{ $announcement->title }}
            </div>

            <div class="announcement-body">
                {!! nl2br(e($announcement->description)) !!}
            </div>

            <a href="{{ route('student.announcements.index') }}" class="btn">View on Dashboard</a>

            <div class="meta-info">
                Posted on: {{ $announcement->created_at->format('F d, Y') }}
            </div>
        </div>

        <div class="footer">
            <p>You are receiving this email because you are a registered student at LincHostel.</p>
            <p>&copy; {{ date('Y') }} Lincoln University Hostel Management. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
