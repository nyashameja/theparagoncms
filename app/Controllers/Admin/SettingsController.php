<?php

namespace App\Controllers\Admin;

use App\Support\Request;
use App\Support\Session;
use App\Support\Logger;
use App\Support\Cache;
use App\Models\Setting;

class SettingsController
{
    public function general(Request $request): string
    {
        return view('admin.settings.general', ['title' => 'General Settings', 'settings' => Setting::all()]);
    }

    public function saveGeneral(Request $request): void
    {
        $fields = [
            'business_name', 'legal_name', 'tagline', 'contact_email', 'contact_phone',
            'whatsapp_number', 'whatsapp_default_message',
            'address_line1', 'address_line2', 'address_city', 'address_province',
            'address_postal', 'address_country',
            'business_hours', 'google_maps_embed',
            'social_facebook', 'social_instagram', 'social_linkedin', 'social_twitter', 'social_youtube',
        ];

        $data = [];
        foreach ($fields as $field) {
            $data[$field] = $request->get($field, '');
        }

        Setting::setMany($data);
        Cache::forget('site_settings');
        Logger::audit('settings_updated', ['section' => 'general']);
        Session::flash('success', 'Settings saved.');
        redirect('/admin/settings/general');
    }

    public function brand(Request $request): string
    {
        return view('admin.settings.brand', ['title' => 'Brand Settings', 'settings' => Setting::all()]);
    }

    public function saveBrand(Request $request): void
    {
        $fields = [
            'logo_primary', 'logo_dark', 'logo_icon', 'favicon',
            'color_primary_bg', 'color_alt_bg', 'color_primary_text', 'color_secondary_text',
            'color_strong_black', 'color_accent', 'color_accent_dark', 'color_border',
            'color_success', 'color_warning',
            'font_primary', 'font_secondary',
        ];

        $data = [];
        foreach ($fields as $field) {
            $data[$field] = $request->get($field, '');
        }

        Setting::setMany($data);
        Cache::forget('site_settings');
        Logger::audit('settings_updated', ['section' => 'brand']);
        Session::flash('success', 'Brand settings saved.');
        redirect('/admin/settings/brand');
    }

    public function seo(Request $request): string
    {
        return view('admin.settings.seo', ['title' => 'SEO Settings', 'settings' => Setting::all()]);
    }

    public function saveSeo(Request $request): void
    {
        $fields = [
            'site_title', 'site_description', 'default_og_image',
            'google_analytics_id', 'google_search_console_verification',
            'meta_pixel_id', 'meta_pixel_enabled',
            'schema_org_type', 'schema_sameAs',
            'robots_txt_content',
        ];

        $data = [];
        foreach ($fields as $field) {
            $data[$field] = $request->get($field, '');
        }

        Setting::setMany($data);
        Cache::forget('site_settings');
        Logger::audit('settings_updated', ['section' => 'seo']);
        Session::flash('success', 'SEO settings saved.');
        redirect('/admin/settings/seo');
    }

    public function email(Request $request): string
    {
        return view('admin.settings.email', ['title' => 'Email Settings', 'settings' => Setting::all()]);
    }

    public function saveEmail(Request $request): void
    {
        $fields = [
            'mail_host', 'mail_port', 'mail_username', 'mail_encryption',
            'mail_from_name', 'mail_from_address', 'lead_notification_emails',
        ];

        $data = [];
        foreach ($fields as $field) {
            $data[$field] = $request->get($field, '');
        }

        // Store password only if provided
        $password = $request->get('mail_password', '');
        if ($password) {
            $data['mail_password'] = $password;
        }

        Setting::setMany($data);
        Logger::audit('settings_updated', ['section' => 'email']);
        Session::flash('success', 'Email settings saved.');
        redirect('/admin/settings/email');
    }

    public function testEmail(Request $request): void
    {
        $to = Session::get('user_email') ?? Setting::get('contact_email', '');
        if (!$to) {
            Session::flash('error', 'No email address to send test to.');
            redirect('/admin/settings/email');
        }

        $sent = \App\Support\Mailer::to($to)
            ->subject('SMTP Test — The Paragon .Design')
            ->html('<p>This is a test email from your Paragon .Design CMS. If you received this, your email settings are working correctly.</p>')
            ->send();

        Session::flash($sent ? 'success' : 'error', $sent ? "Test email sent to $to." : 'Failed to send test email. Check your SMTP settings.');
        redirect('/admin/settings/email');
    }

    public function integrations(Request $request): string
    {
        return view('admin.settings.integrations', ['title' => 'Integrations', 'settings' => Setting::all()]);
    }

    public function saveIntegrations(Request $request): void
    {
        $fields = [
            'leadforge_webhook_url', 'leadforge_api_key',
            'auditforge_webhook_url', 'auditforge_api_key',
            'newsletter_provider', 'newsletter_api_key', 'newsletter_list_id',
            'booking_provider', 'booking_url',
            'recaptcha_site_key', 'recaptcha_secret_key',
        ];

        $data = [];
        foreach ($fields as $field) {
            $data[$field] = $request->get($field, '');
        }

        Setting::setMany($data);
        Logger::audit('settings_updated', ['section' => 'integrations']);
        Session::flash('success', 'Integration settings saved.');
        redirect('/admin/settings/integrations');
    }

    public function maintenanceMode(Request $request): void
    {
        $enabled = (bool) $request->get('maintenance_mode', 0);
        Setting::set('maintenance_mode', $enabled ? '1' : '0');
        Cache::forget('site_settings');
        Logger::audit('maintenance_mode', ['enabled' => $enabled]);
        Session::flash('success', 'Maintenance mode ' . ($enabled ? 'enabled' : 'disabled') . '.');
        redirect('/admin/settings/general');
    }
}
