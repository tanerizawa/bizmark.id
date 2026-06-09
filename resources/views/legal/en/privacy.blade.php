@extends('new.layouts.app')

@section('title', 'Privacy Policy - Bizmark.ID')
@section('description', 'Bizmark.ID Privacy Policy regarding the collection, use, and protection of your personal data.')

@section('content')
@php
    $contact = (array) data_get(config('landing_metrics'), 'contact', []);
    $supportEmail = $contact['email'] ?? 'info@bizmark.id';
    $phoneRaw = $contact['phone'] ?? '+62 838 7960 2855';
    $phoneHref = 'tel:' . preg_replace('/\s+/', '', $phoneRaw);
@endphp

<section class="relative overflow-hidden pt-28 pb-16 bg-[var(--bg-raised)] border-b border-gray-200">
    <div class="container-wide">
        <div class="max-w-4xl">
            <span class="section-badge mb-4">Legal</span>
            <h1 class="section-title mb-3">Privacy Policy</h1>
            <p class="section-description" style="margin-left:0;">PT Cangah Pajaratan Mandiri (Bizmark.ID) · Last updated: {{ now()->format('F d, Y') }}</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <article class="card">
            <div class="content-prose">
                <h2>Introduction</h2>
                <p>PT Cangah Pajaratan Mandiri ("Bizmark.ID", "we", "us", or "our") is committed to protecting your privacy and the security of your personal information. This Privacy Policy explains how we collect, use, disclose, and protect the information you provide when you use our industrial permit consulting services, access our digital platform, or use free digital tools available on our website.</p>
                <p>By using our services, platform, or digital tools, you agree to the collection and use of information in accordance with this policy. If you do not agree with this policy, please do not use our services.</p>

                <h2>Information We Collect</h2>
                <h3>1. Information You Provide</h3>
                <p>We collect information you provide directly to us, including:</p>
                <ul>
                    <li><strong>Identity Data:</strong> Full name, address, phone number, email address, ID number/tax number</li>
                    <li><strong>Company Data:</strong> Company name, office address, business sector, organizational structure, company documents (deed, legal decrees, business permits, etc.)</li>
                    <li><strong>Permit Data:</strong> Information related to the type of permit applied for, project location, technical facility data, environmental documents</li>
                    <li><strong>Communication Data:</strong> Conversations via email, WhatsApp, phone, or contact forms</li>
                    <li><strong>Transaction Data:</strong> Payment history, invoices, service contracts</li>
                </ul>

                <h3>1.2 Information from Free Digital Tools</h3>
                <p>When you use our free digital tools (such as Polygon SHP Maker or the Permit Calculator), we collect:</p>
                <ul>
                    <li><strong>Applicant Data:</strong> Company name, contact person, email address, and WhatsApp/phone number</li>
                    <li><strong>Project/Location Data:</strong> Land or project name, administrative address (province/city/district/sub-district), geographic coordinates (latitude/longitude), area size, and project notes</li>
                    <li><strong>Consent Records:</strong> Timestamp of your acceptance of Terms &amp; Conditions when using the tools</li>
                </ul>

                <h3>1.3 Automatically Collected Information</h3>
                <p>When you access our website, we may collect technical information automatically, including:</p>
                <ul>
                    <li>IP address and geographic location</li>
                    <li>Browser and operating system information (user agent)</li>
                    <li>Visited pages and visit duration</li>
                    <li>Referral source (how you found us)</li>
                    <li>Cookies and similar tracking technologies</li>
                </ul>

                <h3>1.4 Local Storage (localStorage)</h3>
                <p>Some of our digital tools use localStorage in your browser to save temporary data automatically (auto-save), such as in-progress form entries. This data:</p>
                <ul>
                    <li>Is stored locally on your device, not on our servers</li>
                    <li>Can be removed through browser settings or a tool-specific "Delete Data" action</li>
                    <li>Is used to restore your work if the browser is closed accidentally</li>
                    <li>Is not sent to our server until you actively submit a form</li>
                </ul>

                <h2>How We Use Information</h2>
                <p>We use collected information for the following purposes:</p>
                <ul>
                    <li><strong>Service Delivery:</strong> Processing permit applications, preparing documents, and coordinating with relevant government agencies</li>
                    <li><strong>Communication:</strong> Sending progress updates, key notifications, payment confirmations, and periodic reports</li>
                    <li><strong>Administration:</strong> Managing your account, processing payments, and issuing invoices/contract documents</li>
                    <li><strong>Service Improvement:</strong> Analyzing usage of our website and tools to improve quality and develop new features</li>
                    <li><strong>Legal Compliance:</strong> Fulfilling legal obligations, responding to lawful requests, and protecting rights/security</li>
                    <li><strong>Marketing:</strong> Sending information about new services or offers (with your consent where required)</li>
                    <li><strong>Lead Follow-up:</strong> Following up data collected through free tools to offer consultation services relevant to your project/needs</li>
                </ul>

                <h2>Information Sharing</h2>
                <p>We may share your information with:</p>
                <ul>
                    <li><strong>Government Agencies:</strong> Ministries, departments, and institutions involved in permit processing</li>
                    <li><strong>Consulting Partners:</strong> Specialist consultants (AMDAL, HSE, engineering) supporting your project</li>
                    <li><strong>Service Providers:</strong> IT/cloud/payment providers that support our operations</li>
                    <li><strong>Auditors &amp; Legal Advisors:</strong> For compliance and professional advisory purposes</li>
                    <li><strong>Law Enforcement:</strong> When required by applicable law, court orders, or legal process</li>
                </ul>
                <p>We require relevant third parties to observe confidentiality and use data only as instructed.</p>

                <h2>Data Security</h2>
                <p>We implement technical and organizational safeguards, including:</p>
                <ul>
                    <li>Data encryption in transit (SSL/TLS)</li>
                    <li>Restricted access for authorized personnel only</li>
                    <li>Backup and disaster recovery mechanisms</li>
                    <li>Periodic monitoring for suspicious activity</li>
                    <li>Confidentiality obligations with employees and partners</li>
                </ul>
                <p>However, no internet transmission or storage method is 100% secure. We continuously improve our safeguards but cannot guarantee absolute security.</p>

                <h2>Your Rights</h2>
                <p>Subject to applicable data protection rules, you may have rights to:</p>
                <ul>
                    <li><strong>Access:</strong> Request a copy of personal information we hold about you</li>
                    <li><strong>Correction:</strong> Request updates/corrections of inaccurate data</li>
                    <li><strong>Deletion:</strong> Request deletion of your data (subject to legal exceptions)</li>
                    <li><strong>Restriction:</strong> Request restriction of certain processing activities</li>
                    <li><strong>Portability:</strong> Request transfer of your data to another provider</li>
                    <li><strong>Objection:</strong> Object to certain processing purposes</li>
                    <li><strong>Consent Withdrawal:</strong> Withdraw previously given consent</li>
                </ul>
                <p>To exercise these rights, contact us at <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a>.</p>

                <h2>Data Retention</h2>
                <p>We retain personal information for as long as needed to:</p>
                <ul>
                    <li>Deliver services requested by you</li>
                    <li>Comply with legal/regulatory obligations (including minimum record retention periods)</li>
                    <li>Resolve disputes and enforce agreements</li>
                </ul>
                <p>After retention obligations are met, we will securely delete or anonymize your data.</p>

                <h2>Cookies and Tracking Technologies</h2>
                <p>Our website uses cookies and similar technologies to:</p>
                <ul>
                    <li>Remember your preferences and settings</li>
                    <li>Analyze traffic and user behavior</li>
                    <li>Improve website performance and security</li>
                    <li>Provide more relevant content</li>
                </ul>
                <p>You may configure your browser to reject cookies or notify you when cookies are used. Some features may not work properly without cookies.</p>

                <h2>Third-Party Links</h2>
                <p>Our website may contain links to third-party websites. We are not responsible for their privacy practices or content. Please review each third party’s policy.</p>

                <h2>Policy Changes</h2>
                <p>We may revise this Privacy Policy from time to time. Updates will be posted on this page with a revised "Last updated" date. Continued use of our services after changes indicates acceptance of the updated policy.</p>

                <h2>Children's Privacy</h2>
                <p>Our services are not intended for individuals under 18 years of age. We do not knowingly collect personal information from children.</p>

                <h2>Contact Us</h2>
                <p>If you have questions or concerns about this Privacy Policy, please contact us:</p>
                <ul>
                    <li><strong>Email:</strong> <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a></li>
                    <li><strong>Phone:</strong> <a href="{{ $phoneHref }}">{{ $phoneRaw }}</a></li>
                    <li><strong>Address:</strong> PT Cangah Pajaratan Mandiri, Karawang, West Java 41361, Indonesia</li>
                </ul>
            </div>
        </article>
    </div>
</section>

<section class="section-sm section-premium">
    <div class="container-wide text-center">
        <h2 class="text-gray-900 mb-3" style="font-size:clamp(1.5rem,3vw,2.1rem);font-weight:750;">Need Clarification?</h2>
        <p class="mb-7 text-gray-600">We can help answer questions about how we protect your data.</p>
        <div class="flex flex-wrap justify-center gap-3">
            <a href="mailto:{{ $supportEmail }}" class="btn btn-primary"><i class="fas fa-envelope"></i> Email Us</a>
            <a href="{{ route('landing.en') }}" class="btn btn-ghost"><i class="fas fa-home"></i> Back to Home</a>
        </div>
    </div>
</section>
@endsection
