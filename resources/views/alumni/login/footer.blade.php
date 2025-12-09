<style>
    /* Footer styling */
    footer {
        color: white !important;
        flex-shrink: 0 !important;
        width: 100%;
        margin-top: 0 !important;
    }

    footer .footer-logo {
        filter: none !important;
    }

    footer .social-icon img {
        filter: brightness(0) invert(1);
    }

    footer p, footer div, footer a {
        color: rgba(255, 255, 255, 0.9) !important;
    }

    footer strong {
        color: white !important;
    }

    footer .social-icon {
        background: rgba(255, 255, 255, 0.2) !important;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    footer .social-icon:hover {
        background: rgba(255, 255, 255, 0.3) !important;
    }

    footer > div > div:nth-child(2) {
        border-top: 1px solid rgba(255, 255, 255, 0.2) !important;
    }

    /* Desktop - Footer at bottom */
    @media (min-width: 992px) {
        footer {
            margin-top: 40px !important;
        }
    }

    /* Tablet */
    @media (max-width: 991px) {
        footer > div {
            padding: 0 20px;
        }

        footer > div > div:first-child {
            grid-template-columns: 1fr 1fr !important;
            gap: 30px !important;
        }
    }

    /* Mobile */
    @media (max-width: 767px) {
        footer {
            padding: 30px 16px 16px !important;
        }

        footer > div > div:first-child {
            grid-template-columns: 1fr !important;
            gap: 24px !important;
        }

        footer > div > div:first-child > div:first-child img {
            width: 80px !important;
        }

        footer > div > div:first-child > div p {
            font-size: 13px !important;
        }

        footer > div > div:last-child {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 12px !important;
        }

        footer > div > div:last-child > div:first-child p {
            font-size: 12px !important;
        }

        footer > div > div:last-child > div:last-child a {
            font-size: 12px !important;
        }
    }

    /* Small mobile */
    @media (max-width: 480px) {
        footer {
            padding: 24px 12px 12px !important;
        }

        footer > div > div:first-child {
            gap: 20px !important;
        }

        footer > div > div:first-child > div:first-child img {
            width: 70px !important;
        }

        footer > div > div:first-child > div p {
            font-size: 12px !important;
        }

        footer > div > div:last-child > div:first-child p {
            font-size: 11px !important;
        }
    }
</style>

<!-- Footer -->
<footer style="padding: 20px 20px 20px; margin-top: 40px; background: url('{{ asset('images/social/background.png') }}') center/cover no-repeat, linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); position: relative; overflow: hidden;">
    <!-- Dark Overlay -->
    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.7); z-index: 0;"></div>
    <div style="max-width: 1200px; margin: 0 auto; position: relative; z-index: 1;">
        <!-- Top Section -->
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 40px; margin-bottom: 30px;">
            <!-- Logo and Address -->
            <div>
                <a href="{{ route('alumni.login') }}"><img src="{{ asset('images/logo/footer_logo.png') }}" alt="SIP Abacus" class="footer-logo" style="width: 200px; height: auto; margin-bottom: 20px;"></a>
            </div>
            <div style="color: #b0b0b0; font-size: 14px; line-height: 1.5;">
                <p style="margin: 0; font-weight: 600; color: white;">SIP Academy India Pvt. Ltd.,</p>
                <p style="margin: 0;">Kences Towers, 7th Floor,</p>
                <p style="margin: 0;">1, Ramakrishna Street,</p>
                <p style="margin: 0;">Off. North Usman Road, T. Nagar,</p>
                <p style="margin: 0;">Chennai - 600017.</p>
            </div>

            <!-- Contact Info -->
            <div>
                <div style="color: #b0b0b0; font-size: 14px; line-height: 2;">
                    <p style="margin: 0;"><strong style="color: white;">Phone:</strong> 044-42023131/42605609</p>
                    <p style="margin: 0;"><strong style="color: white;">Email:</strong> sipinfo@sipacademyindia.com</p>
                </div>
            </div>

            <!-- Social Media -->
            <div>
                <p style="margin: 0 0 16px 0; font-weight: 600; font-size: 16px; color: white;">Follow Us</p>
                <div style="display: flex;">
                    <a href="#" style="align-items: center; transition: all 0.3s;"
                       onmouseover="this.style.transform='scale(1.1)'" 
                       onmouseout="this.style.transform='scale(1)'">
                        <img src="{{ asset('images/social/fb.png') }}" alt="Facebook" style="width: 30px; height: 30px; object-fit: contain;">
                    </a>
                    <a href="#" style="align-items: center; transition: all 0.3s; margin-left: 10px;"
                       onmouseover="this.style.transform='scale(1.1)'" 
                       onmouseout="this.style.transform='scale(1)'">
                        <img src="{{ asset('images/social/instra.png') }}" alt="Instagram" style="width: 30px; height: 30px; object-fit: contain;">
                    </a>
                    <a href="#" style="align-items: center; transition: all 0.3s;"
                       onmouseover="this.style.transform='scale(1.1)'" 
                       onmouseout="this.style.transform='scale(1)'">
                        <img src="{{ asset('images/social/youtube.png') }}" alt="YouTube" style="width: 60px; height: 30px; object-fit: contain;">
                    </a>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div style="border-top: 1px solid #333; margin: 30px 0 20px;"></div>

        <!-- Bottom Section -->
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <div style="color: #808080; font-size: 13px;">
                <p style="margin: 0 0 4px 0;">Design & Developed by <a href="https://pixelstudios.in/" style=" text-decoration:none; transition: color 0.3s;"onmouseover="this.style.color='#ecd608'"onmouseout="this.style.color='#ffffff'">Pixel Studios.</a></p>
                <p style="margin: 0;">©2025 SIP Academy India Pvt. Ltd. | All rights reserved.</p>
            </div>
            <div>
                <a href="https://sipabacus.com/in/privacy-policy" style="color: #b0b0b0; text-decoration: none; font-size: 13px; transition: color 0.3s;"
                   onmouseover="this.style.color='#dc2626'" onmouseout="this.style.color='#b0b0b0'">
                    Privacy Policy
                </a>
            </div>
        </div>
    </div>
</footer>
