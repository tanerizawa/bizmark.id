@extends('new.layouts.app')

@section('title', 'Terms & Conditions - Bizmark.ID')
@section('description', 'Terms and Conditions for using Bizmark.ID permit consultation services.')

@section('content')
@php
    $contact = (array) data_get(config('landing_metrics'), 'contact', []);
    $supportEmail = $contact['email'] ?? 'info@bizmark.id';
    $phoneRaw = $contact['phone'] ?? '+62 838 7960 2855';
    $phoneHref = 'tel:' . preg_replace('/\s+/', '', $phoneRaw);
    $whatsappLink = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';
@endphp

<section class="relative overflow-hidden pt-28 pb-16 bg-[var(--bg-raised)] border-b border-gray-200">
    <div class="container-wide">
        <div class="max-w-4xl">
            <span class="section-badge mb-4">Legal</span>
            <h1 class="section-title mb-3">Terms &amp; Conditions</h1>
            <p class="section-description" style="margin-left:0;">PT Cangah Pajaratan Mandiri (Bizmark.ID) · Last updated: {{ now()->format('F d, Y') }}</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <article class="card">
            <div class="content-prose">
                <h2>Introduction</h2>
                <p>Welcome to Bizmark.ID. These Terms &amp; Conditions govern the use of all services provided by PT Cangah Pajaratan Mandiri ("Bizmark.ID", "we", "us", or "our"), including industrial/business permit consulting services, digital platforms (website/applications), and free digital tools available on our website. By using our services, you ("Client", "User", "you", or "your") agree to be bound by these Terms.</p>
                <p>If you do not agree with any part of these terms, please do not use our services.</p>

                <h2>1. Acceptance of Terms</h2>
                <p>By accessing our website, contacting us, or using our services, you acknowledge that:</p>
                <ul>
                    <li>You have read, understood, and agreed to these Terms &amp; Conditions</li>
                    <li>You have legal capacity to enter into this agreement</li>
                    <li>You represent a legitimate business entity with valid legal documents (for paid services)</li>
                    <li>The information you provide is accurate and complete</li>
                    <li>You will comply with all applicable provisions</li>
                    <li>Use of free digital tools is also subject to these Terms &amp; Conditions</li>
                </ul>

                <h2>2. Service Definition</h2>
                <p>Bizmark.ID provides industrial permit consulting and permit-processing services, including but not limited to:</p>
                <ul>
                    <li><strong>AMDAL &amp; UKL-UPL:</strong> Environmental impact and management documents</li>
                    <li><strong>Environmental Approvals:</strong> Environmental approvals for business activities</li>
                    <li><strong>PROPER:</strong> Environmental performance rating support</li>
                    <li><strong>Business Licensing (OSS):</strong> Risk-based licensing support through OSS</li>
                    <li><strong>IPAL &amp; SLO:</strong> Wastewater treatment and operational eligibility support</li>
                    <li><strong>IMB/PBG &amp; SLF:</strong> Building and functional eligibility permits/certification support</li>
                    <li><strong>Occupational Safety Certification:</strong> Safety and health compliance support</li>
                    <li><strong>HSE Consultation:</strong> Health, Safety, and Environment consulting</li>
                    <li><strong>Digital Environmental Monitoring:</strong> Monitoring systems with dashboards and digital workflows</li>
                </ul>
                <h3>2.2 Free Digital Tools</h3>
                <p>In addition to paid services, Bizmark.ID provides free digital tools on our platform, including:</p>
                <ul>
                    <li><strong>Polygon SHP Maker:</strong> A shapefile (.shp) creation tool for OSS/land planning/project mapping needs</li>
                    <li><strong>Permit Calculator:</strong> A permit cost/time estimation tool based on industry type and permit category</li>
                </ul>
                <p>These free tools are provided on an "as-is" basis for user assistance. Their use is subject to these Terms &amp; Conditions and the applicable Privacy Policy.</p>

                <h2>3. Client Responsibilities</h2>
                <h3>3.1 Document Provision</h3>
                <p>Clients must provide:</p>
                <ul>
                    <li>Complete and current company documents (deed, legal approvals, tax documents, etc.)</li>
                    <li>Accurate and detailed technical project/facility data</li>
                    <li>Required environmental and operational information</li>
                    <li>Power of attorney and other supporting documents where needed</li>
                    <li>Site access for surveys and inspections</li>
                </ul>
                <h3>3.2 Information Accuracy</h3>
                <p>Clients warrant that all provided information/documents are true, accurate, and not misleading. Clients remain responsible for legal consequences arising from inaccurate/incomplete information.</p>
                <h3>3.3 Cooperation</h3>
                <p>Clients must provide timely cooperation, including responding to information requests, providing access, and facilitating coordination with related stakeholders.</p>

                <h2>4. Fees and Payments</h2>
                <h3>4.1 Fee Structure</h3>
                <p>Service fees are provided in official quotations and may include consulting/document processing fees, site survey/technical analysis costs, government administrative fees, and project-related supporting costs.</p>
                <h3>4.2 Payment Scheme</h3>
                <p>Payment is typically made in stages:</p>
                <ul>
                    <li><strong>Down Payment:</strong> generally 30-50% upon contract signing</li>
                    <li><strong>Progress Payment:</strong> generally 30-40% on key milestones (e.g., draft completion)</li>
                    <li><strong>Final Payment:</strong> generally 20-30% when permit/documents are completed and delivered</li>
                </ul>
                <p>Exact percentages and milestones may vary based on agreement in the contract.</p>
                <h3>4.3 Late Payment</h3>
                <p>Late payment may cause delays and/or penalties according to agreement. We may suspend service where payment remains overdue beyond the agreed threshold.</p>
                <h3>4.4 Additional Fees</h3>
                <p>Additional costs outside initial quotation (e.g., major scope change, major revisions, unexpected government costs) will be communicated and require client approval before implementation.</p>

                <h2>5. Timelines</h2>
                <h3>5.1 Project Timeline</h3>
                <p>Estimated completion timelines are defined based on project complexity, document readiness, and external authority processes. Timelines are estimates and may change due to factors beyond our control.</p>
                <h3>5.2 Delays Beyond Control</h3>
                <p>We are not responsible for delays caused by:</p>
                <ul>
                    <li>Client delays in providing documents/information</li>
                    <li>Review/approval process duration at government institutions</li>
                    <li>Regulatory or policy changes</li>
                    <li>Force majeure events (natural disasters, pandemics, social disruption, etc.)</li>
                    <li>Major revisions requested by clients or authorities</li>
                </ul>

                <h2>6. Limitation of Liability</h2>
                <h3>6.1 Scope of Responsibility</h3>
                <p>Bizmark.ID is responsible for preparing permit documentation according to applicable standards, coordinating with relevant authorities, providing progress updates, and maintaining client confidentiality.</p>
                <h3>6.2 Limitations</h3>
                <p>We are not responsible for:</p>
                <ul>
                    <li>Permit rejection due to objective site/project conditions</li>
                    <li>Client financial/operational losses caused by permit process duration</li>
                    <li>Policy/regulatory changes during execution</li>
                    <li>Inaccurate/incomplete information submitted by clients</li>
                    <li>Acts/omissions of third parties (authorities, external consultants, etc.)</li>
                    <li>Force majeure impacts</li>
                </ul>
                <h3>6.3 Liability Cap</h3>
                <p>Where claims arise, our total liability is limited to contract value paid for the relevant service. We are not liable for indirect/consequential losses or loss of profit.</p>

                <h2>7. Confidentiality</h2>
                <p>Both parties agree to:</p>
                <ul>
                    <li>Maintain confidentiality of business, technical, and personal information obtained during cooperation</li>
                    <li>Not disclose confidential information to third parties without written consent</li>
                    <li>Use confidential information solely for service execution</li>
                    <li>Return or destroy confidential information after the engagement ends</li>
                </ul>
                <p>Confidentiality obligations do not apply to information that is publicly available, legally required to be disclosed, or independently developed.</p>

                <h2>8. Intellectual Property</h2>
                <p>Intellectual property ownership is defined as follows:</p>
                <ul>
                    <li><strong>Project Documents:</strong> Client-specific permit documents become client property after full payment</li>
                    <li><strong>Templates &amp; Methodologies:</strong> Bizmark.ID templates, methods, and know-how remain Bizmark.ID property</li>
                    <li><strong>Client Data:</strong> Data/information provided by the client remains client property</li>
                    <li><strong>Portfolio:</strong> We may reference project experience in portfolio form without disclosing sensitive data, unless otherwise agreed</li>
                </ul>

                <h2>9. Termination</h2>
                <h3>9.1 Termination by Client</h3>
                <p>Clients may terminate service with prior written notice under the agreed terms. Clients remain responsible for completed work and incurred costs.</p>
                <h3>9.2 Termination by Bizmark.ID</h3>
                <p>We may terminate service where:</p>
                <ul>
                    <li>Client materially breaches these terms</li>
                    <li>Payment is overdue beyond agreed tolerance</li>
                    <li>Required information/documents are not provided despite reminders</li>
                    <li>False or misleading information is identified</li>
                    <li>Prolonged force majeure occurs</li>
                </ul>

                <h2>10. Dispute Resolution</h2>
                <p>In case of dispute:</p>
                <ol>
                    <li>Parties will first seek amicable settlement within a reasonable period</li>
                    <li>If unresolved, mediation may be used</li>
                    <li>If mediation fails, arbitration or court process may apply based on governing agreement/law</li>
                    <li>Applicable law is the law of the Republic of Indonesia</li>
                    <li>Jurisdiction/forum follows the agreed legal domicile</li>
                </ol>

                <h2>11. Use of Digital Tools</h2>
                <h3>11.1 Terms for Using Free Digital Tools</h3>
                <p>By using free digital tools on Bizmark.ID (including Polygon SHP Maker, Permit Calculator, and similar tools), you agree that:</p>
                <ul>
                    <li>You must provide accurate data when completing required forms</li>
                    <li>Submitted data (including company/contact/location data) may be stored as lead data for commercial follow-up</li>
                    <li>Bizmark.ID may contact you via email/WhatsApp/phone for relevant service offers</li>
                    <li>You are responsible for the accuracy of submitted data</li>
                    <li>Tool outputs are estimates and do not constitute legal determinations</li>
                </ul>
                <h3>11.2 Data Collected Through Digital Tools</h3>
                <p>When using free digital tools, the following may be collected and stored:</p>
                <ul>
                    <li><strong>Applicant Data:</strong> Company name, contact person, email, and WhatsApp/phone number</li>
                    <li><strong>Project/Location Data:</strong> Project name, administrative address, coordinates, and project notes</li>
                    <li><strong>Technical Data:</strong> IP address, browser information (user agent), and terms-consent timestamp</li>
                    <li><strong>Local Storage Data:</strong> Browser localStorage may keep temporary form data for auto-save</li>
                </ul>
                <h3>11.3 Limitation of Liability for Digital Tools</h3>
                <p>Bizmark.ID is not responsible for:</p>
                <ul>
                    <li>Inaccuracies in calculated/output results from digital tools</li>
                    <li>Losses resulting from use of tool outputs as sole business/legal basis</li>
                    <li>Technical failures, service interruption, or unavailability of digital tools</li>
                    <li>Loss of data stored locally in user browsers</li>
                    <li>Third-party misuse of generated files/data</li>
                </ul>
                <h3>11.4 Prohibited Use</h3>
                <p>Users are prohibited from:</p>
                <ul>
                    <li>Using digital tools for unlawful purposes</li>
                    <li>Scraping, reverse engineering, or automated exploitation of digital tools</li>
                    <li>Submitting false or misleading data</li>
                    <li>Using tools in ways that damage/disrupt/overload our systems</li>
                    <li>Redistributing or modifying digital tools without written permission</li>
                </ul>

                <h2>12. Changes to Terms</h2>
                <p>We may update these Terms &amp; Conditions from time to time. Updates will be posted on our website with a revised "Last updated" date. For ongoing contractual projects, the contract version may prevail as agreed. Continued use of new services after updates indicates acceptance of revised terms.</p>

                <h2>13. Miscellaneous</h2>
                <h3>13.1 Entire Agreement</h3>
                <p>These Terms, together with related contracts and supporting documents, form the entire agreement between parties for relevant services.</p>
                <h3>13.2 Severability</h3>
                <p>If any provision is deemed invalid or unenforceable, the remaining provisions remain in full force.</p>
                <h3>13.3 Waiver</h3>
                <p>Failure to enforce any provision does not constitute waiver of rights under that provision.</p>
                <h3>13.4 Assignment</h3>
                <p>Clients may not assign rights/obligations under this agreement without our written consent. We may assign rights/obligations as permitted by applicable law/contract.
                </p>

                <h2>Contact Us</h2>
                <p>If you have questions about these Terms &amp; Conditions, please contact us:</p>
                <ul>
                    <li><strong>Email:</strong> <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a></li>
                    <li><strong>Phone:</strong> <a href="{{ $phoneHref }}">{{ $phoneRaw }}</a></li>
                    <li><strong>WhatsApp:</strong> <a href="{{ $whatsappLink }}" target="_blank" rel="noopener">{{ $phoneRaw }}</a></li>
                    <li><strong>Address:</strong> PT Cangah Pajaratan Mandiri, Karawang, West Java 41361, Indonesia</li>
                </ul>
            </div>
        </article>
    </div>
</section>

<section class="section-sm section-premium">
    <div class="container-wide text-center">
        <h2 class="text-gray-900 mb-3" style="font-size:clamp(1.5rem,3vw,2.1rem);font-weight:750;">Ready to Talk?</h2>
        <p class="mb-7 text-gray-600">Share your context and we'll help map the right permits and next steps.</p>
        <div class="flex flex-wrap justify-center gap-3">
            <a href="{{ $whatsappLink }}" target="_blank" rel="noopener" class="btn btn-primary"><i class="fab fa-whatsapp"></i> WhatsApp</a>
            <a href="{{ route('landing.en') }}" class="btn btn-ghost"><i class="fas fa-home"></i> Back to Home</a>
        </div>
    </div>
</section>
@endsection
