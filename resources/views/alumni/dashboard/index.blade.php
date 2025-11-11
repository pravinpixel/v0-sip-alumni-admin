@extends('alumni.layouts.index')

@section('content')
<div style="max-width: 1200px;">
    <h2 style="font-size: 28px; font-weight: 700; margin-bottom: 10px; color: #222;">Alumni Dashboard</h2>
    <p style="color: #666; margin-bottom: 30px;">Welcome back! Here's your activity overview</p>

    <!-- Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <!-- Card 1 -->
        <div style="background: #fff; padding: 24px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                <div style="background: #dc2626; width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i class="fa fa-users" style="font-size: 24px; color: #fff;"></i>
                </div>
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 15V5M5 10H15" stroke="#10b981" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
            <p style="font-size: 28px; font-weight: 700; color: #222; margin-bottom: 4px;">24</p>
            <p style="color: #666; font-size: 14px; font-weight: 500; margin-bottom: 2px;">Connections Made</p>
            <p style="color: #999; font-size: 12px;">Total accepted invites</p>
        </div>

        <!-- Card 2 -->
        <div style="background: #fff; padding: 24px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                <div style="background: #f59e0b; width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i class="fa fa-clock-o" style="font-size: 24px; color: #fff;"></i>
                </div>
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 15V5M5 10H15" stroke="#10b981" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
            <p style="font-size: 28px; font-weight: 700; color: #222; margin-bottom: 4px;">5</p>
            <p style="color: #666; font-size: 14px; font-weight: 500; margin-bottom: 2px;">Pending Requests</p>
            <p style="color: #999; font-size: 12px;">Awaiting response</p>
        </div>

        <!-- Card 3 -->
        <div style="background: #fff; padding: 24px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                <div style="background: #dc2626; width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i class="fa fa-file-text" style="font-size: 24px; color: #fff;"></i>
                </div>
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 15V5M5 10H15" stroke="#10b981" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
            <p style="font-size: 28px; font-weight: 700; color: #222; margin-bottom: 4px;">12</p>
            <p style="color: #666; font-size: 14px; font-weight: 500; margin-bottom: 2px;">Posts Created</p>
            <p style="color: #999; font-size: 12px;">Community contributions</p>
        </div>

        <!-- Card 4 -->
        <div style="background: #fff; padding: 24px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                <div style="background: #f59e0b; width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i class="fa fa-heart" style="font-size: 24px; color: #fff;"></i>
                </div>
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 15V5M5 10H15" stroke="#10b981" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
            <p style="font-size: 28px; font-weight: 700; color: #222; margin-bottom: 4px;">156</p>
            <p style="color: #666; font-size: 14px; font-weight: 500; margin-bottom: 2px;">Total Engagement</p>
            <p style="color: #999; font-size: 12px;">Likes & replies received</p>
        </div>
    </div>

    <!-- Recent Activity -->
    <div style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
        <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px; color: #222;">Recent Activity</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #e0e0e0;">
                    <th style="padding: 12px; text-align: left; color: #666; font-weight: 600; font-size: 14px;">Activity</th>
                    <th style="padding: 12px; text-align: left; color: #666; font-weight: 600; font-size: 14px;">Date</th>
                    <th style="padding: 12px; text-align: left; color: #666; font-weight: 600; font-size: 14px;">Status</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom: 1px solid #e0e0e0;">
                    <td style="padding: 12px; color: #333;">New connection request from John Doe</td>
                    <td style="padding: 12px; color: #666;">Today</td>
                    <td style="padding: 12px;"><span style="background-color: #dbeafe; color: #1e40af; padding: 4px 12px; border-radius: 4px; font-size: 12px;">Pending</span></td>
                </tr>
                <tr style="border-bottom: 1px solid #e0e0e0;">
                    <td style="padding: 12px; color: #333;">You posted on the forum</td>
                    <td style="padding: 12px; color: #666;">Yesterday</td>
                    <td style="padding: 12px;"><span style="background-color: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 4px; font-size: 12px;">Completed</span></td>
                </tr>
                <tr>
                    <td style="padding: 12px; color: #333;">Event: Alumni Meetup 2024</td>
                    <td style="padding: 12px; color: #666;">2 days ago</td>
                    <td style="padding: 12px;"><span style="background-color: #fef3c7; color: #92400e; padding: 4px 12px; border-radius: 4px; font-size: 12px;">Attended</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
