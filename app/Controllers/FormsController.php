<?php

namespace App\Controllers;

use App\Support\Request;
use App\Support\Session;
use App\Support\Validator;
use App\Support\Database;
use App\Support\Mailer;
use App\Support\Logger;
use App\Support\RateLimit;
use App\Models\Lead;
use App\Models\Setting;

class FormsController
{
    public function contact(Request $request): string
    {
        return view('public.forms.contact', [
            'title'       => 'Contact Us | The Paragon .Design',
            'description' => 'Send us a message. Our team in Johannesburg will be in touch soon.',
        ]);
    }

    public function submitContact(Request $request): void
    {
        $this->checkSpam($request);

        $v = Validator::make($request->body, [
            'name'    => 'required|min:2|max:100',
            'email'   => 'required|email',
            'message' => 'required|min:10|max:3000',
            'consent' => 'required',
        ]);

        if ($v->fails()) {
            Session::flash('errors', $v->errors());
            Session::flash('_old', array_diff_key($request->body, ['message' => '']));
            redirect('/contact');
        }

        $leadId = $this->createLead($request, 'contact', [
            'name'    => $request->get('name'),
            'email'   => $request->get('email'),
            'phone'   => $request->get('phone', ''),
            'company' => $request->get('company', ''),
            'message' => $request->get('message'),
        ]);

        $this->sendNotification($request, 'General Contact', $leadId);
        $this->sendConfirmation($request->get('email'), $request->get('name'), 'contact');

        Session::flash('success', 'Thank you for your message. We\'ll be in touch within one business day.');
        redirect('/contact?submitted=1');
    }

    public function quote(Request $request): string
    {
        $services = \App\Models\Service::published();
        return view('public.forms.quote', [
            'title'       => 'Request a Quote | The Paragon .Design',
            'description' => 'Tell us about your project. We\'ll review your brief and get back to you with a tailored proposal.',
            'services'    => $services,
        ]);
    }

    public function submitQuote(Request $request): void
    {
        $this->checkSpam($request);

        $v = Validator::make($request->body, [
            'name'         => 'required|min:2|max:100',
            'email'        => 'required|email',
            'service_type' => 'required',
            'description'  => 'required|min:20|max:5000',
            'consent'      => 'required',
        ]);

        if ($v->fails()) {
            Session::flash('errors', $v->errors());
            Session::flash('_old', $request->body);
            redirect('/quote');
        }

        $leadId = $this->createLead($request, 'quote', $request->body);
        $this->handleFileAttachments($request, $leadId);
        $this->sendNotification($request, 'Quote Request', $leadId);
        $this->sendConfirmation($request->get('email'), $request->get('name'), 'quote');

        Session::flash('success', 'Your project brief has been received. We\'ll review it and get back to you within one business day.');
        redirect('/quote?submitted=1');
    }

    public function consultation(Request $request): string
    {
        return view('public.forms.consultation', [
            'title'       => 'Book a Consultation | The Paragon .Design',
            'description' => 'Book a free 30-minute consultation with our team. No obligation.',
        ]);
    }

    public function submitConsultation(Request $request): void
    {
        $this->checkSpam($request);

        $v = Validator::make($request->body, [
            'name'  => 'required|min:2|max:100',
            'email' => 'required|email',
            'phone' => 'required',
            'consent' => 'required',
        ]);

        if ($v->fails()) {
            Session::flash('errors', $v->errors());
            Session::flash('_old', $request->body);
            redirect('/consultation');
        }

        $leadId = $this->createLead($request, 'consultation', $request->body);
        $this->sendNotification($request, 'Consultation Booking', $leadId);
        $this->sendConfirmation($request->get('email'), $request->get('name'), 'consultation');

        Session::flash('success', 'Your consultation request has been received. We\'ll confirm your booking shortly.');
        redirect('/consultation?submitted=1');
    }

    public function audit(Request $request): string
    {
        return view('public.forms.audit', [
            'title'       => 'Free Website Audit | The Paragon .Design',
            'description' => 'Get a free website audit from The Paragon .Design. Performance, SEO, mobile, and conversion analysis.',
        ]);
    }

    public function submitAudit(Request $request): void
    {
        $this->checkSpam($request);

        $v = Validator::make($request->body, [
            'name'        => 'required|min:2|max:100',
            'email'       => 'required|email',
            'website_url' => 'required|url',
            'consent'     => 'required',
        ]);

        if ($v->fails()) {
            Session::flash('errors', $v->errors());
            Session::flash('_old', $request->body);
            redirect('/website-audit');
        }

        $leadId = $this->createLead($request, 'audit', $request->body);

        // Create audit request
        $auditId = Database::insert(
            'INSERT INTO audit_requests (lead_id, website_url, name, email, phone, company, notes, status, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())',
            [
                $leadId,
                $request->get('website_url'),
                $request->get('name'),
                $request->get('email'),
                $request->get('phone', ''),
                $request->get('company', ''),
                $request->get('notes', ''),
                'pending',
            ]
        );

        $this->sendNotification($request, 'Website Audit Request', $leadId);
        $this->sendConfirmation($request->get('email'), $request->get('name'), 'audit');

        Session::flash('success', 'Your audit request has been submitted. We\'ll deliver your free website audit within 3-5 business days.');
        redirect('/website-audit?submitted=1');
    }

    public function newsletter(Request $request): void
    {
        $email = filter_var(trim($request->get('email', '')), FILTER_VALIDATE_EMAIL);
        if (!$email) {
            Session::flash('error', 'Please enter a valid email address.');
            redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }

        $existing = Database::selectOne('SELECT id FROM newsletter_subscribers WHERE email = ?', [$email]);
        if (!$existing) {
            $consent = !empty($request->get('consent')) ? 1 : 0;
            Database::insert(
                'INSERT INTO newsletter_subscribers (email, consent, ip_address, created_at) VALUES (?, ?, ?, NOW())',
                [$email, $consent, $request->ip]
            );
        }

        Session::flash('success', 'You\'ve been subscribed to our newsletter.');
        redirect($_SERVER['HTTP_REFERER'] ?? '/');
    }

    private function checkSpam(Request $request): void
    {
        // Rate limiting
        if (!RateLimit::check('form:' . $request->ip, 5, 300)) {
            abort(429, 'Too many requests.');
        }

        // Honeypot
        if ($request->get('website_verify', '') !== '') {
            Logger::info('Honeypot triggered', ['ip' => $request->ip]);
            Session::flash('success', 'Thank you for your message.');
            redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }

        // Minimum completion time (5 seconds)
        $startTime = (int) $request->get('_form_start', 0);
        if ($startTime && (time() - $startTime) < 5) {
            Logger::info('Form submitted too fast', ['ip' => $request->ip]);
            Session::flash('success', 'Thank you for your message.');
            redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }
    }

    private function createLead(Request $request, string $formType, array $data): int|string
    {
        $duplicate = Lead::detectDuplicate($data['email'] ?? '', $data['phone'] ?? '');

        $leadData = [
            'name'         => $data['name'] ?? '',
            'email'        => $data['email'] ?? '',
            'phone'        => $data['phone'] ?? '',
            'company'      => $data['company'] ?? '',
            'service_type' => $data['service_type'] ?? $formType,
            'message'      => $data['message'] ?? $data['description'] ?? '',
            'form_type'    => $formType,
            'status'       => Lead::STATUS_NEW,
            'priority'     => 'normal',
            'source'       => $request->header('Referer') ? 'website' : 'direct',
            'landing_page' => $request->header('Referer', ''),
            'ip_address'   => $request->ip,
            'user_agent'   => $request->header('User-Agent', ''),
            'utm_source'   => $request->get('utm_source', ''),
            'utm_medium'   => $request->get('utm_medium', ''),
            'utm_campaign' => $request->get('utm_campaign', ''),
            'consent'      => !empty($data['consent']) ? 1 : 0,
            'consent_at'   => date('Y-m-d H:i:s'),
            'raw_data'     => json_encode($data),
            'is_duplicate' => $duplicate ? 1 : 0,
        ];

        $leadId = Lead::create($leadData);

        // Webhook to LeadForge
        $this->pushToLeadForge($leadData, (int) $leadId);

        return $leadId;
    }

    private function sendNotification(Request $request, string $formLabel, int|string $leadId): void
    {
        $recipients = Setting::get('lead_notification_emails', Setting::get('contact_email', ''));
        if (!$recipients) return;

        foreach (explode(',', $recipients) as $email) {
            $email = trim($email);
            if (!$email) continue;
            Mailer::to($email)
                ->subject("New $formLabel — The Paragon .Design")
                ->view('emails.lead-notification', ['formLabel' => $formLabel, 'leadId' => $leadId, 'data' => $request->body])
                ->send();
        }
    }

    private function sendConfirmation(string $email, string $name, string $type): void
    {
        Mailer::to($email, $name)
            ->subject('We\'ve received your message — The Paragon .Design')
            ->view("emails.confirmation-$type", ['name' => $name])
            ->send();
    }

    private function handleFileAttachments(Request $request, int|string $leadId): void
    {
        if (empty($request->files['attachments'])) return;

        $files = $request->files['attachments'];
        // Normalise single/multiple
        if (!is_array($files['name'])) {
            $files = ['name' => [$files['name']], 'type' => [$files['type']], 'tmp_name' => [$files['tmp_name']], 'error' => [$files['error']], 'size' => [$files['size']]];
        }

        $allowed = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'zip'];
        $dir     = storage_path('private-uploads/leads/' . date('Y/m'));
        if (!is_dir($dir)) mkdir($dir, 0700, true);

        foreach ($files['name'] as $i => $name) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) continue;
            if ($files['size'][$i] > 10 * 1024 * 1024) continue;

            $safeName = bin2hex(random_bytes(8)) . '.' . $ext;
            move_uploaded_file($files['tmp_name'][$i], "$dir/$safeName");

            Database::insert(
                'INSERT INTO lead_attachments (lead_id, original_name, stored_path, file_size, created_at) VALUES (?, ?, ?, ?, NOW())',
                [$leadId, basename($name), "private-uploads/leads/" . date('Y/m') . "/$safeName", $files['size'][$i]]
            );
        }
    }

    private function pushToLeadForge(array $data, int $leadId): void
    {
        $webhookUrl = Setting::get('leadforge_webhook_url', '');
        if (!$webhookUrl) return;

        try {
            $payload = json_encode(['lead_id' => $leadId, 'data' => $data]);
            $ch = curl_init($webhookUrl);
            curl_setopt_array($ch, [
                CURLOPT_POST       => true,
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_TIMEOUT    => 5,
                CURLOPT_RETURNTRANSFER => true,
            ]);
            curl_exec($ch);
            curl_close($ch);
        } catch (\Throwable $e) {
            Logger::error('LeadForge webhook failed', ['error' => $e->getMessage()]);
        }
    }
}
