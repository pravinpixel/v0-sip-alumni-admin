@extends('alumni.layouts.index')

@section('title', 'Directory - Alumni Tracking')

@section('content')
    <div style="margin-bottom: 30px;">
        <h2 style="font-size: 24px; font-weight: 700; color: #333; margin-bottom: 8px;">Alumni Directory</h2>
        <p style="color: #666; font-size: 14px; margin-bottom: 20px;">Connect with 15 alumni from SIP Academy</p>

        <!-- Added search bar and filter controls -->
        <div style="display: flex; gap: 15px; margin-bottom: 20px;">
            <div style="flex: 1; position: relative;">
                <input type="text" placeholder="Search alumni..." style="width: 100%; padding: 12px 15px; border: 1px solid #e0e0e0; border-radius: 6px; font-size: 14px; background-color: white;">
            </div>
            <button style="padding: 12px 20px; border: 1px solid #e0e0e0; background-color: white; border-radius: 6px; cursor: pointer; font-size: 14px; color: #333;">⬇ Filter</button>
        </div>

        <!-- Added info banner -->
        <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; border-radius: 4px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
            <p style="color: #856404; font-size: 14px; margin: 0;">You can share your contact with alumni. Once they accept, you can view their profile and contact info in the Connections menu.</p>
            <button style="background: none; border: none; color: #856404; font-size: 18px; cursor: pointer;">✕</button>
        </div>

        <!-- Created table layout for alumni list -->
        <table style="width: 100%; border-collapse: collapse; background-color: white; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-radius: 6px; overflow: hidden;">
            <thead>
                <tr style="background: linear-gradient(90deg, #c41e3a 0%, #c41e3a 30%, #ff8c42 70%, #ff8c42 100%); color: white; font-weight: 700; font-size: 14px;">
                    <th style="padding: 15px; text-align: left; border-right: 1px solid rgba(255,255,255,0.3);">Alumni ⬇</th>
                    <th style="padding: 15px; text-align: left; border-right: 1px solid rgba(255,255,255,0.3);">Batch ⬇</th>
                    <th style="padding: 15px; text-align: left; border-right: 1px solid rgba(255,255,255,0.3);">Location ⬇</th>
                    <th style="padding: 15px; text-align: left;">Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Alumni Row 1 -->
                <tr style="border-bottom: 1px solid #e8e8e8;">
                    <td style="padding: 15px; display: flex; align-items: center; gap: 12px;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #e8e8e8; display: flex; align-items: center; justify-content: center; font-size: 20px;">👤</div>
                        <div>
                            <div style="font-weight: 700; color: #333; font-size: 14px;">Rajesh Kumar</div>
                            <div style="color: #999; font-size: 12px;">Senior Software Engineer</div>
                        </div>
                    </td>
                    <td style="padding: 15px;">
                        <span style="background-color: #fff3cd; color: #ff8c42; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">2019</span>
                    </td>
                    <td style="padding: 15px; color: #666; font-size: 13px;">Bangalore, India</td>
                    <td style="padding: 15px;">
                        <button style="background-color: #c41e3a; color: white; padding: 7px 14px; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: 600;">Share Contact</button>
                    </td>
                </tr>

                <!-- Alumni Row 2 -->
                <tr style="border-bottom: 1px solid #e8e8e8;">
                    <td style="padding: 15px; display: flex; align-items: center; gap: 12px;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #e8e8e8; display: flex; align-items: center; justify-content: center; font-size: 20px;">👤</div>
                        <div>
                            <div style="font-weight: 700; color: #333; font-size: 14px;">Priya Sharma</div>
                            <div style="color: #999; font-size: 12px;">Hardware Engineer</div>
                        </div>
                    </td>
                    <td style="padding: 15px;">
                        <span style="background-color: #fff3cd; color: #ff8c42; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">2020</span>
                    </td>
                    <td style="padding: 15px; color: #666; font-size: 13px;">Mumbai, India</td>
                    <td style="padding: 15px;">
                        <span style="color: #999; font-size: 12px;">Contact Shared</span>
                    </td>
                </tr>

                <!-- Alumni Row 3 -->
                <tr style="border-bottom: 1px solid #e8e8e8;">
                    <td style="padding: 15px; display: flex; align-items: center; gap: 12px;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #ff8c42; display: flex; align-items: center; justify-content: center; font-size: 20px; color: white; font-weight: 700;">AP</div>
                        <div>
                            <div style="font-weight: 700; color: #333; font-size: 14px;">Amit Patel</div>
                            <div style="color: #999; font-size: 12px;">Design Engineer</div>
                        </div>
                    </td>
                    <td style="padding: 15px;">
                        <span style="background-color: #fff3cd; color: #ff8c42; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">2018</span>
                    </td>
                    <td style="padding: 15px; color: #666; font-size: 13px;">Pune, India</td>
                    <td style="padding: 15px;">
                        <button style="background-color: #ff8c42; color: white; padding: 7px 14px; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: 600;">Contact Accepted</button>
                    </td>
                </tr>

                <!-- Alumni Row 4 -->
                <tr style="border-bottom: 1px solid #e8e8e8;">
                    <td style="padding: 15px; display: flex; align-items: center; gap: 12px;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #e8e8e8; display: flex; align-items: center; justify-content: center; font-size: 20px;">👤</div>
                        <div>
                            <div style="font-weight: 700; color: #333; font-size: 14px;">Sneha Reddy</div>
                            <div style="color: #999; font-size: 12px;">DevOps Engineer</div>
                        </div>
                    </td>
                    <td style="padding: 15px;">
                        <span style="background-color: #fff3cd; color: #ff8c42; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">2021</span>
                    </td>
                    <td style="padding: 15px; color: #666; font-size: 13px;">Hyderabad, India</td>
                    <td style="padding: 15px;">
                        <button style="background-color: #f8d7da; color: #721c24; padding: 7px 14px; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: 600; margin-right: 8px;">Contact Rejected</button>
                        <button style="background-color: white; color: #c41e3a; padding: 7px 14px; border: 1px solid #c41e3a; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: 600;">↻ Reshare</button>
                    </td>
                </tr>

                <!-- Alumni Row 5 -->
                <tr>
                    <td style="padding: 15px; display: flex; align-items: center; gap: 12px;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #e8e8e8; display: flex; align-items: center; justify-content: center; font-size: 20px;">👤</div>
                        <div>
                            <div style="font-weight: 700; color: #333; font-size: 14px;">Vikram Singh</div>
                            <div style="color: #999; font-size: 12px;">Project Manager</div>
                        </div>
                    </td>
                    <td style="padding: 15px;">
                        <span style="background-color: #fff3cd; color: #ff8c42; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">2019</span>
                    </td>
                    <td style="padding: 15px; color: #666; font-size: 13px;">Delhi, India</td>
                    <td style="padding: 15px;">
                        <button style="background-color: #c41e3a; color: white; padding: 7px 14px; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: 600;">Share Contact</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection
