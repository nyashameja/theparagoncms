<?php

namespace App\Controllers\Public;

use App\Support\Request;
use App\Support\Session;
use App\Support\Validator;
use App\Support\Database;
use App\Support\View;
use App\Support\Logger;
use App\Support\RateLimit;
use App\Support\Mailer;

class FormsController
{
    /* ── Display forms ──────────────────────────────────────── */

    public function contact(Request $request): void
    {
        $services = Database::select(
            "SELECT id, name FROM services WHERE status='published' AND deleted_at IS NULL ORDER BY sort_order ASC"
        );
        echo View::render('contact/index', [
            'title'           => 'Contact Us — The Paragon .Design',
            'metaDescription' => 'Send us a message. We respond within one business day.',
            'services'        => $services,
        ]);
    }

    public function quote(Request $request): void
    {
        $services = Database::select(
            "SELECT id, name FROM services WHERE status='published' AND deleted_at IS NULL ORDER BY sort_order ASC"
        );
        echo View::render('forms/quote', [
            'title'           => 'Get a Free Quote — The Paragon .Design',
            'metaDescription' => 'Request a tailored quote for web design, branding or digital marketing.',
            'services'        => $services,
        ]);
    }

    public function consultation(Request $request): void
    {
        echo View::render('forms/consultation', [
            'title'           => 'Book a Free Consultation — The Paragon .Design',
            'metaDescription' => 'Book a free 30-minute strategy call with our team. No obligation.',
        ]);
    }

    public function audit(Request $request): void
    {
        echo View::render('forms/audit', [
            'title'           => 'Free Website Audit — The Paragon .Design',
            'metaDescription' => 'Get a free performance, SEO, mobile, and security audit for your website.',
        ]);
    }

    /* ── Form submissions ───────────────────────────────────── */

    public function submitContact(Request $request): void
    {
        $this->guardSpam($request, '/contact');

        $v = Validator::make($request->all(), [
            'name'    => 'required|min:2|max:100',
            'email'   => 'required|email',
            'message' => 'required|min:10|max:2000',
            'consent' => 'required',
        ]);
        $errors = $v->errors();

        if ($v->fails()) {
            $services = Database::select(
                "SELECT id, name FROM services WHERE status='published' AND deleted_at IS NULL ORDER BY sort_order ASC"
            );
            echo View::render('contact/index', [
                'title'    => 'Contact Us — The Paragon .Design',
                'services' => $services,
                'errors'   => $errors,
            ]);
            return;
        }

        $leadId = $this->saveLead($request, 'contact');
        $this->notifyTeam($request, 'Contact Enquiry', $leadId);
        $this->confirmToUser($request->input('email'), $request->input('name', ''), 'contact');

        echo View::render('contact/index', [
            'title'    => 'Contact Us — The Paragon .Design',
            'services' => [],
            'success'  => true,
        ]);
    }

    public function submitQuote(Request $request): void
    {
        $this->guardSpam($request, '/get-quote');

        $v = Validator::make($request->all(), [
            'name'    => 'required|min:2|max:100',
            'email'   => 'required|email',
            'service' => 'required',
            'message' => 'required|min:20|max:3000',
            'consent' => 'required',
        ]);
        $errors = $v->errors();

        if ($v->fails()) {
            $services = Database::select(
                "SELECT id, name FROM services WHERE status='published' AND deleted_at IS NULL ORDER BY sort_order ASC"
            );
            echo View::render('forms/quote', ['title' => 'Get a Free Quote', 'services' => $services, 'errors' => $errors]);
            return;
        }

        $leadId = $this->saveLead($request, 'quote');
        $this->notifyTeam($request, 'Quote Request', $leadId);
        $this->confirmToUser($request->input('email'), $request->input('name', ''), 'quote');

        echo View::render('forms/quote', ['title' => 'Get a Free Quote', 'services' => [], 'success' => true]);
    }

    public function submitConsultation(Request $request): void
    {
        $this->guardSpam($request, '/book-consultation');

        $v = Validator::make($request->all(), [
            'name'    => 'required|min:2|max:100',
            'email'   => 'required|email',
            'topic'   => 'required|min:10|max:1000',
            'consent' => 'required',
        ]);
        $errors = $v->errors();

        if ($v->fails()) {
            echo View::render('forms/consultation', ['title' => 'Book Consultation', 'errors' => $errors]);
            return;
        }

        $leadId = $this->saveLead($request, 'consultation');
        $this->notifyTeam($request, 'Consultation Request', $leadId);
        $this->confirmToUser($request->input('email'), $request->input('name', ''), 'consultation');

        echo View::render('forms/consultation', ['title' => 'Book Consultation', 'success' => true]);
    }

    public function submitAudit(Request $request): void
    {
        $this->guardSpam($request, '/free-audit');

        $v = Validator::make($request->all(), [
            'name'        => 'required|min:2|max:100',
            'email'       => 'required|email',
            'website_url' => 'required|url',
            'consent'     => 'required',
        ]);
        $errors = $v->errors();

        if ($v->fails()) {
            echo View::render('forms/audit', ['title' => 'Free Website Audit', 'errors' => $errors]);
            return;
        }

        $leadId = $this->saveLead($request, 'audit');

        /* Create audit request */
        Database::insert(
            "INSERT INTO audit_requests (lead_id, website_url, name, email, phone, company, notes, status, created_at)
             VALUES (?,?,?,?,?,?,?,'pending',NOW())",
            [
                $leadId,
                $request->input('website_url'),
                $request->input('name'),
                $request->input('email'),
                $request->input('phone', ''),
                $request->input('company', ''),
                $request->input('notes', ''),
            ]
        );

        $this->notifyTeam($request, 'Website Audit Request', $leadId);
        $this->confirmToUser($request->input('email'), $request->input('name', ''), 'audit');

        echo View::render('forms/audit', ['title' => 'Free Website Audit', 'success' => true]);
    }

    /* ── Private helpers ────────────────────────────────────── */

    private function guardSpam(Request $request, string $backUrl): void
    {
        if (!RateLimit::check('form:' . $request->ip, 5, 300)) {
            abort(429);
        }
        /* Honeypot */
        if ($request->input('website', '') !== '' || $request->input('website_hp', '') !== '') {
            Logger::info('Honeypot triggered', ['ip' => $request->ip]);
            http_response_code(200);
            exit;
        }
        /* Minimum form-fill time: 4 seconds */
        $t = (int)$request->input('hp_time', 0);
        if ($t && (time() - intdiv($t, 1000)) < 4) {
            Logger::info('Form submitted too fast', ['ip' => $request->ip]);
            http_response_code(200);
            exit;
        }
    }

    private function saveLead(Request $request, string $formType): int
    {
        $data = $request->all();
        return (int)Database::insert(
            "INSERT INTO leads (name, email, phone, company, message, form_type, status, priority,
             source, ip_address, user_agent, utm_source, utm_medium, utm_campaign,
             consent, consent_at, created_at, updated_at)
             VALUES (?,?,?,?,?,?,'new','normal',?,?,?,?,?,?,1,NOW(),NOW(),NOW())",
            [
                $data['name']     ?? '',
                $data['email']    ?? '',
                $data['phone']    ?? '',
                $data['company']  ?? '',
                $data['message']  ?? $data['topic'] ?? $data['notes'] ?? '',
                $formType,
                'website',
                $request->ip,
                $_SERVER['HTTP_USER_AGENT'] ?? '',
                $data['utm_source']   ?? '',
                $data['utm_medium']   ?? '',
                $data['utm_campaign'] ?? '',
            ]
        );
    }

    private function notifyTeam(Request $request, string $label, int $leadId): void
    {
        $to = setting('lead_notification_email') ?: setting('contact_email');
        if (!$to) return;
        try {
            $data = $request->all();
            $html = $this->renderEmail('contact-notification', [
                'name'         => $data['name'] ?? '',
                'email'        => $data['email'] ?? '',
                'phone'        => $data['phone'] ?? '',
                'company'      => $data['company'] ?? '',
                'service'      => $data['service'] ?? '',
                'message'      => $data['message'] ?? $data['topic'] ?? '',
                'submitted_at' => date('d M Y H:i'),
                'admin_url'    => url('/admin/leads/' . $leadId),
            ]);
            Mailer::to($to)->subject("New {$label} — " . setting('site_name'))->html($html)->send();
        } catch (\Throwable $e) {
            Logger::error('Lead notification failed', ['error' => $e->getMessage()]);
        }
    }

    private function confirmToUser(string $email, string $name, string $type): void
    {
        try {
            $template = match($type) {
                'audit' => 'audit-confirmation',
                default => 'lead-confirmation',
            };
            $html = $this->renderEmail($template, [
                'name'       => $name,
                'first_name' => explode(' ', $name)[0],
                'email'      => $email,
                'site_url'   => setting('app_url', url('/')),
                'whatsapp_number' => setting('whatsapp_number', ''),
                'website_url' => $_POST['website_url'] ?? '',
            ]);
            Mailer::to($email, $name)->subject('We received your enquiry — ' . setting('site_name'))->html($html)->send();
        } catch (\Throwable $e) {
            Logger::error('Confirmation email failed', ['error' => $e->getMessage()]);
        }
    }

    private function renderEmail(string $template, array $vars): string
    {
        $file = BASE_PATH . '/resources/emails/' . $template . '.php';
        if (!file_exists($file)) return '';
        extract($vars, EXTR_SKIP);
        ob_start();
        include $file;
        return ob_get_clean();
    }
}
