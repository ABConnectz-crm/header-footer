<?php
/**
 * Kumar Footer Widget - Enhanced Version with Background Overlay
 */

if (!defined('ABSPATH')) {
    exit;
}

class Kumar_Footer_Widget extends \Elementor\Widget_Base {
    
    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);
        
        // Register AJAX handlers
        add_action('wp_ajax_kumar_newsletter_subscribe', [$this, 'handle_newsletter_subscription']);
        add_action('wp_ajax_nopriv_kumar_newsletter_subscribe', [$this, 'handle_newsletter_subscription']);
    }
    
    /**
     * Handle newsletter subscription AJAX
     */
    public function handle_newsletter_subscription() {
        check_ajax_referer('kumar_newsletter_nonce', 'nonce');
        
        $email = sanitize_email($_POST['email']);
        $widget_id = sanitize_text_field($_POST['widget_id']);
        
        if (!is_email($email)) {
            wp_send_json_error(['message' => 'Please enter a valid email address.']);
        }
        
        // Get widget settings to retrieve admin email
        $admin_email = get_option('kumar_newsletter_admin_email_' . $widget_id, get_option('admin_email'));
        
        // Send email to admin
        $subject = 'New Newsletter Subscription - Kumar Jewelers';
        $message = "New newsletter subscription:\n\n";
        $message .= "Email: " . $email . "\n";
        $message .= "Date: " . date('F j, Y, g:i a') . "\n";
        $message .= "IP Address: " . $_SERVER['REMOTE_ADDR'] . "\n";
        
        $headers = ['Content-Type: text/plain; charset=UTF-8'];
        
        $sent = wp_mail($admin_email, $subject, $message, $headers);
        
        if ($sent) {
            // Store subscriber email
            $subscribers = get_option('kumar_newsletter_subscribers', []);
            if (!in_array($email, $subscribers)) {
                $subscribers[] = $email;
                update_option('kumar_newsletter_subscribers', $subscribers);
            }
            
            wp_send_json_success(['message' => 'Thank you for subscribing! We\'ll keep you updated with our latest collections.']);
        } else {
            wp_send_json_error(['message' => 'Something went wrong. Please try again later.']);
        }
    }
    
    
    public function get_name() {
        return 'kumar_footer';
    }
    
    public function get_title() {
        return __('Kumar Footer', 'kumar-jewelers');
    }
    
    public function get_icon() {
        return 'eicon-footer';
    }
    
    public function get_categories() {
        return ['general'];
    }
    
    public function get_keywords() {
        return ['footer', 'contact', 'locations', 'kumar', 'jewelers'];
    }
    
    protected function register_controls() {
        
        // ========== LOCATIONS HERO SECTION ==========
        $this->start_controls_section(
            'locations_section',
            [
                'label' => __('Our Locations (Hero Section)', 'kumar-jewelers'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'locations_heading',
            [
                'label' => __('Section Heading', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Visit Our Stores',
            ]
        );
        
        $this->add_control(
            'locations_description',
            [
                'label' => __('Description', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => 'Experience our collections in person at our Bay Area locations',
            ]
        );
        
        $locations_repeater = new \Elementor\Repeater();
        
        $locations_repeater->add_control(
            'store_name',
            [
                'label' => __('Store Name', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Fremont',
            ]
        );
        
        $locations_repeater->add_control(
            'store_address',
            [
                'label' => __('Address', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => '5162 Mowry Ave<br>Fremont, CA 94538',
            ]
        );
        
        $locations_repeater->add_control(
            'store_phone',
            [
                'label' => __('Phone Display', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '(510) 713-1040',
            ]
        );
        
        $locations_repeater->add_control(
            'store_phone_link',
            [
                'label' => __('Phone Link', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'tel:5107131040',
            ]
        );
        
        $locations_repeater->add_control(
            'store_email',
            [
                'label' => __('Email (Optional)', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '',
            ]
        );
        
        $this->add_control(
            'store_locations',
            [
                'label' => __('Store Locations', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $locations_repeater->get_controls(),
                'default' => [
                    [
                        'store_name' => 'Fremont',
                        'store_address' => '5162 Mowry Ave<br>Fremont, CA 94538',
                        'store_phone' => '(510) 713-1040',
                        'store_phone_link' => 'tel:5107131040',
                    ],
                    [
                        'store_name' => 'Dublin',
                        'store_address' => '4548 Dublin Blvd<br>Dublin, CA 94568',
                        'store_phone' => '(925) 248-2280',
                        'store_phone_link' => 'tel:9252482280',
                    ],
                ],
                'title_field' => '{{{ store_name }}}',
            ]
        );
        
        $this->end_controls_section();
        
        // ========== NEWSLETTER (IN LOCATIONS SECTION) ==========
        $this->start_controls_section(
            'newsletter_section',
            [
                'label' => __('Newsletter (Below Locations)', 'kumar-jewelers'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'show_newsletter',
            [
                'label' => __('Show Newsletter', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'kumar-jewelers'),
                'label_off' => __('No', 'kumar-jewelers'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'newsletter_admin_email',
            [
                'label' => __('Admin Email (Receive Notifications)', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => get_option('admin_email'),
                'description' => __('Enter the email address where subscription notifications will be sent', 'kumar-jewelers'),
                'condition' => ['show_newsletter' => 'yes'],
            ]
        );
        
        $this->add_control(
            'newsletter_heading',
            [
                'label' => __('Heading', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Stay Connected',
                'condition' => ['show_newsletter' => 'yes'],
            ]
        );
        
        $this->add_control(
            'newsletter_text',
            [
                'label' => __('Description', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Subscribe for exclusive offers & latest collections',
                'condition' => ['show_newsletter' => 'yes'],
            ]
        );
        
        $this->add_control(
            'newsletter_placeholder',
            [
                'label' => __('Input Placeholder', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Your email address',
                'condition' => ['show_newsletter' => 'yes'],
            ]
        );
        
        $this->add_control(
            'newsletter_button_text',
            [
                'label' => __('Button Text', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Subscribe',
                'condition' => ['show_newsletter' => 'yes'],
            ]
        );
        
        $this->end_controls_section();
        
        // ========== BRAND SECTION ==========
        $this->start_controls_section(
            'brand_section',
            [
                'label' => __('Brand', 'kumar-jewelers'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'logo_type',
            [
                'label' => __('Logo Type', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'text' => __('Text Logo', 'kumar-jewelers'),
                    'image' => __('Image Logo', 'kumar-jewelers'),
                ],
                'default' => 'image',
            ]
        );
        
        $this->add_control(
            'logo_text',
            [
                'label' => __('Logo Text', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'KUMAR JEWELERS',
                'condition' => ['logo_type' => 'text'],
            ]
        );
        
        $this->add_control(
            'logo_image',
            [
                'label' => __('Logo Image', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
                'condition' => ['logo_type' => 'image'],
            ]
        );
        
        $this->add_responsive_control(
            'logo_width',
            [
                'label' => __('Logo Width', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => ['min' => 50, 'max' => 400, 'step' => 5],
                    '%' => ['min' => 10, 'max' => 100],
                ],
                'default' => ['size' => 200, 'unit' => 'px'],
                'selectors' => [
                    '{{WRAPPER}} .kumar-footer-logo-image' => 'width: {{SIZE}}{{UNIT}}',
                ],
                'condition' => ['logo_type' => 'image'],
            ]
        );
        
        $this->add_control(
            'brand_tagline',
            [
                'label' => __('Brand Tagline', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Jewelry that honors faith & style',
            ]
        );
        
        $this->add_control(
            'brand_description',
            [
                'label' => __('Description', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => 'Crafting timeless elegance for every celebration of life. From traditional heritage pieces to contemporary designs, we bring your precious moments to life.',
            ]
        );
        
        $this->end_controls_section();
        
        // ========== SOCIAL LINKS ==========
        $this->start_controls_section(
            'social_section',
            [
                'label' => __('Social Media', 'kumar-jewelers'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'social_heading',
            [
                'label' => __('Section Heading', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Follow Our Journey',
            ]
        );
        
        $social_repeater = new \Elementor\Repeater();
        
        $social_repeater->add_control(
            'social_icon',
            [
                'label' => __('Icon', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fab fa-facebook-f',
                    'library' => 'fa-brands',
                ],
                'recommended' => [
                    'fa-brands' => [
                        'facebook-f',
                        'instagram',
                        'twitter',
                        'pinterest-p',
                        'youtube',
                        'linkedin-in',
                        'tiktok',
                        'whatsapp',
                    ],
                ],
            ]
        );
        
        $social_repeater->add_control(
            'social_label',
            [
                'label' => __('Label', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Facebook',
            ]
        );
        
        $social_repeater->add_control(
            'social_link',
            [
                'label' => __('Link', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::URL,
                'default' => ['url' => '#'],
            ]
        );
        
        $this->add_control(
            'social_links',
            [
                'label' => __('Social Links', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $social_repeater->get_controls(),
                'default' => [
                    [
                        'social_icon' => ['value' => 'fab fa-facebook-f', 'library' => 'fa-brands'],
                        'social_label' => 'Facebook',
                        'social_link' => ['url' => '#']
                    ],
                    [
                        'social_icon' => ['value' => 'fab fa-instagram', 'library' => 'fa-brands'],
                        'social_label' => 'Instagram',
                        'social_link' => ['url' => '#']
                    ],
                ],
                'title_field' => '{{{ social_label }}}',
            ]
        );
        
        $this->end_controls_section();
        
        // ========== COLLECTIONS ==========
        $this->start_controls_section(
            'collections_section',
            [
                'label' => __('Collections', 'kumar-jewelers'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'collections_heading',
            [
                'label' => __('Section Heading', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Collections',
            ]
        );
        
        $collections_repeater = new \Elementor\Repeater();
        
        $collections_repeater->add_control(
            'collection_text',
            [
                'label' => __('Text', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Collection Item',
            ]
        );
        
        $collections_repeater->add_control(
            'collection_link',
            [
                'label' => __('Link', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::URL,
                'default' => ['url' => '#'],
            ]
        );
        
        $this->add_control(
            'collections_links',
            [
                'label' => __('Collection Links', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $collections_repeater->get_controls(),
                'default' => [
                    ['collection_text' => 'Bridal Jewelry', 'collection_link' => ['url' => '#bridal']],
                    ['collection_text' => 'Gold Collection', 'collection_link' => ['url' => '#gold']],
                    ['collection_text' => 'Diamonds', 'collection_link' => ['url' => '#diamond']],
                    ['collection_text' => '24ct Bangles', 'collection_link' => ['url' => '#bangles']],
                    ['collection_text' => 'Festival Specials', 'collection_link' => ['url' => '#festival']],
                    ['collection_text' => 'Custom Design', 'collection_link' => ['url' => '#custom']],
                ],
                'title_field' => '{{{ collection_text }}}',
            ]
        );
        
        $this->end_controls_section();
        
        // ========== SERVICES ==========
        $this->start_controls_section(
            'services_section',
            [
                'label' => __('Services', 'kumar-jewelers'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'services_heading',
            [
                'label' => __('Section Heading', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Services',
            ]
        );
        
        $services_repeater = new \Elementor\Repeater();
        
        $services_repeater->add_control(
            'service_text',
            [
                'label' => __('Text', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Service Item',
            ]
        );
        
        $services_repeater->add_control(
            'service_link',
            [
                'label' => __('Link', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::URL,
                'default' => ['url' => '#'],
            ]
        );
        
        $this->add_control(
            'services_links',
            [
                'label' => __('Service Links', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $services_repeater->get_controls(),
                'default' => [
                    ['service_text' => 'Repairs & Resizing', 'service_link' => ['url' => '#repairs']],
                    ['service_text' => 'Custom Jewelry', 'service_link' => ['url' => '#custom']],
                    ['service_text' => 'Gold Exchange', 'service_link' => ['url' => '#exchange']],
                    ['service_text' => 'Certification', 'service_link' => ['url' => '#certification']],
                    ['service_text' => 'Free Consultation', 'service_link' => ['url' => '#consultation']],
                ],
                'title_field' => '{{{ service_text }}}',
            ]
        );
        
        $this->end_controls_section();
        
        // ========== FOOTER BOTTOM ==========
        $this->start_controls_section(
            'footer_bottom_section',
            [
                'label' => __('Footer Bottom', 'kumar-jewelers'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'copyright_text',
            [
                'label' => __('Copyright Text', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '&copy; 2025 Kumar Jewelers. All Rights Reserved.',
            ]
        );
        
        $this->add_control(
            'made_with_love_text',
            [
                'label' => __('Made With Love Text', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Crafted with ♥ in California',
            ]
        );
        
        $bottom_links_repeater = new \Elementor\Repeater();
        
        $bottom_links_repeater->add_control(
            'link_text',
            [
                'label' => __('Text', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Link',
            ]
        );
        
        $bottom_links_repeater->add_control(
            'link_url',
            [
                'label' => __('URL', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::URL,
                'default' => ['url' => '#'],
            ]
        );
        
        $this->add_control(
            'bottom_links',
            [
                'label' => __('Bottom Links', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $bottom_links_repeater->get_controls(),
                'default' => [
                    ['link_text' => 'Privacy Policy', 'link_url' => ['url' => '#privacy']],
                    ['link_text' => 'Terms of Service', 'link_url' => ['url' => '#terms']],
                    ['link_text' => 'Sitemap', 'link_url' => ['url' => '#sitemap']],
                ],
                'title_field' => '{{{ link_text }}}',
            ]
        );
        
        $this->end_controls_section();
        
        // ========== STYLE SECTIONS ==========
        
        // Style: Footer Container
        $this->start_controls_section(
            'style_footer_container',
            [
                'label' => __('Footer Container', 'kumar-jewelers'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'footer_background',
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .kumar-footer',
                'fields_options' => [
                    'background' => ['default' => 'gradient'],
                    'color' => ['default' => 'rgba(139, 21, 56, 0.95)'],
                    'color_b' => ['default' => 'rgba(107, 18, 39, 0.95)'],
                    'gradient_angle' => ['default' => ['size' => 135, 'unit' => 'deg']],
                ],
            ]
        );
        
        $this->add_responsive_control(
            'footer_padding',
            [
                'label' => __('Padding', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'default' => [
                    'top' => '80',
                    'right' => '60',
                    'bottom' => '0',
                    'left' => '60',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .kumar-footer-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );
        
        $this->add_control(
            'border_color',
            [
                'label' => __('Border/Divider Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'rgba(212, 175, 55, 0.2)',
            ]
        );
        
        $this->end_controls_section();
        
        // Style: Locations Hero Background
        $this->start_controls_section(
            'style_locations_background',
            [
                'label' => __('Locations Hero Background', 'kumar-jewelers'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'locations_hero_background',
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .kumar-locations-hero',
                'fields_options' => [
                    'background' => ['default' => 'classic'],
                    'color' => ['default' => '#F5E6D3'],
                ],
            ]
        );
        
        $this->add_control(
            'enable_pattern_overlay',
            [
                'label' => __('Enable Pattern Overlay', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'kumar-jewelers'),
                'label_off' => __('No', 'kumar-jewelers'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );
        
        $this->add_control(
            'pattern_overlay_image',
            [
                'label' => __('Pattern/Overlay Image', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'condition' => ['enable_pattern_overlay' => 'yes'],
            ]
        );
        
        $this->add_control(
            'pattern_overlay_opacity',
            [
                'label' => __('Overlay Opacity', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => ['min' => 0, 'max' => 1, 'step' => 0.05],
                ],
                'default' => ['size' => 0.1],
                'condition' => ['enable_pattern_overlay' => 'yes'],
                'selectors' => [
                    '{{WRAPPER}} .kumar-locations-hero::after' => 'opacity: {{SIZE}}',
                ],
            ]
        );
        
        $this->add_control(
            'pattern_blend_mode',
            [
                'label' => __('Blend Mode', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'normal' => __('Normal', 'kumar-jewelers'),
                    'multiply' => __('Multiply', 'kumar-jewelers'),
                    'screen' => __('Screen', 'kumar-jewelers'),
                    'overlay' => __('Overlay', 'kumar-jewelers'),
                    'soft-light' => __('Soft Light', 'kumar-jewelers'),
                ],
                'default' => 'multiply',
                'condition' => ['enable_pattern_overlay' => 'yes'],
                'selectors' => [
                    '{{WRAPPER}} .kumar-locations-hero::after' => 'mix-blend-mode: {{VALUE}}',
                ],
            ]
        );
        
        $this->end_controls_section();
        
        // Style: Locations Hero Content
        $this->start_controls_section(
            'style_locations_hero',
            [
                'label' => __('Locations Hero Content', 'kumar-jewelers'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'locations_heading_typography',
                'label' => __('Heading Typography', 'kumar-jewelers'),
                'selector' => '{{WRAPPER}} .kumar-locations-hero h2',
                'fields_options' => [
                    'font_family' => ['default' => 'Cormorant Garamond'],
                    'font_size' => ['default' => ['size' => 56, 'unit' => 'px']],
                    'font_weight' => ['default' => '600'],
                ],
            ]
        );
        
        $this->add_control(
            'locations_heading_color',
            [
                'label' => __('Heading Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#8B1538',
                'selectors' => [
                    '{{WRAPPER}} .kumar-locations-hero h2' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'locations_description_color',
            [
                'label' => __('Description Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#6B1227',
                'selectors' => [
                    '{{WRAPPER}} .kumar-locations-hero-desc' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'location_card_bg',
            [
                'label' => __('Card Background', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .kumar-store-card' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'location_card_border',
            [
                'label' => __('Card Border Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#E8D7C3',
                'selectors' => [
                    '{{WRAPPER}} .kumar-store-card' => 'border-color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'location_card_shadow',
                'selector' => '{{WRAPPER}} .kumar-store-card',
                'fields_options' => [
                    'box_shadow_type' => ['default' => 'yes'],
                    'box_shadow' => [
                        'default' => [
                            'horizontal' => 0,
                            'vertical' => 10,
                            'blur' => 30,
                            'spread' => 0,
                            'color' => 'rgba(0, 0, 0, 0.1)',
                        ],
                    ],
                ],
            ]
        );
        
        $this->add_responsive_control(
            'locations_hero_padding',
            [
                'label' => __('Section Padding', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .kumar-locations-container' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}}; padding-top: {{TOP}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );
        
        $this->add_responsive_control(
            'location_cards_gap',
            [
                'label' => __('Cards Gap', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => ['min' => 0, 'max' => 100],
                ],
                'default' => ['size' => 30, 'unit' => 'px'],
                'selectors' => [
                    '{{WRAPPER}} .kumar-store-cards' => 'gap: {{SIZE}}{{UNIT}}',
                ],
            ]
        );
        
        
        $this->add_control(
            'location_name_color',
            [
                'label' => __('Store Name Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#8B1538',
                'selectors' => [
                    '{{WRAPPER}} .kumar-store-name' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'location_info_color',
            [
                'label' => __('Address Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#5A4A3A',
                'selectors' => [
                    '{{WRAPPER}} .kumar-store-info' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'location_phone_color',
            [
                'label' => __('Phone Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#D4AF37',
                'selectors' => [
                    '{{WRAPPER}} .kumar-store-phone' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->end_controls_section();
        
        // Style: Animation Settings
        $this->start_controls_section(
            'style_animations',
            [
                'label' => __('Animation Settings', 'kumar-jewelers'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'enable_scroll_animations',
            [
                'label' => __('Enable Scroll Animations', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'kumar-jewelers'),
                'label_off' => __('No', 'kumar-jewelers'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'animation_duration',
            [
                'label' => __('Animation Duration (ms)', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 800,
                'min' => 200,
                'max' => 2000,
                'step' => 100,
                'condition' => ['enable_scroll_animations' => 'yes'],
            ]
        );
        
        $this->add_control(
            'animation_delay',
            [
                'label' => __('Stagger Delay Between Items (ms)', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 150,
                'min' => 0,
                'max' => 500,
                'step' => 50,
                'condition' => ['enable_scroll_animations' => 'yes'],
            ]
        );
        
        $this->end_controls_section();
        
        // Style: Newsletter
        $this->start_controls_section(
            'style_newsletter',
            [
                'label' => __('Newsletter', 'kumar-jewelers'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'newsletter_heading_color',
            [
                'label' => __('Heading Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFD700',
                'selectors' => [
                    '{{WRAPPER}} .kumar-newsletter h4' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'newsletter_text_color',
            [
                'label' => __('Text Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFE5D9',
                'selectors' => [
                    '{{WRAPPER}} .kumar-newsletter p' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'newsletter_input_bg',
            [
                'label' => __('Input Background', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'rgba(255, 255, 255, 0.1)',
                'selectors' => [
                    '{{WRAPPER}} .kumar-newsletter-input' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'newsletter_input_border',
            [
                'label' => __('Input Border Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'rgba(212, 175, 55, 0.3)',
                'selectors' => [
                    '{{WRAPPER}} .kumar-newsletter-input' => 'border-color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'newsletter_button_bg',
            [
                'label' => __('Button Background', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#D4AF37',
                'selectors' => [
                    '{{WRAPPER}} .kumar-newsletter-button' => 'background: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'newsletter_button_color',
            [
                'label' => __('Button Text Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#1a0f0a',
                'selectors' => [
                    '{{WRAPPER}} .kumar-newsletter-button' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->end_controls_section();
        
        // Style: Brand Logo
        $this->start_controls_section(
            'style_brand_logo',
            [
                'label' => __('Brand Logo', 'kumar-jewelers'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'brand_logo_typography',
                'label' => __('Text Logo Typography', 'kumar-jewelers'),
                'selector' => '{{WRAPPER}} .kumar-footer-logo-text',
                'condition' => ['logo_type' => 'text'],
                'fields_options' => [
                    'font_family'=> ['default' => 'Cormorant Garamond'],
                    'font_size' => ['default' => ['size' => 48, 'unit' => 'px']],
                    'font_weight' => ['default' => '700'],
                ],
            ]
        );
        
        $this->add_control(
            'brand_logo_color_start',
            [
                'label' => __('Logo Gradient Start', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFD700',
                'condition' => ['logo_type' => 'text'],
            ]
        );
        
        $this->add_control(
            'brand_logo_color_end',
            [
                'label' => __('Logo Gradient End', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#D4AF37',
                'condition' => ['logo_type' => 'text'],
            ]
        );
        
        $this->add_responsive_control(
            'logo_margin_bottom',
            [
                'label' => __('Logo Spacing', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 0, 'max' => 50]],
                'default' => ['size' => 20, 'unit' => 'px'],
                'selectors' => [
                    '{{WRAPPER}} .kumar-footer-logo' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
            ]
        );
        
        $this->end_controls_section();
        
        // Style: Brand Content
        $this->start_controls_section(
            'style_brand_content',
            [
                'label' => __('Brand Content', 'kumar-jewelers'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'brand_tagline_color',
            [
                'label' => __('Tagline Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#C9A961',
                'selectors' => [
                    '{{WRAPPER}} .kumar-footer-brand-tagline' => 'color: {{VALUE}} !important',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'brand_tagline_typography',
                'label' => __('Tagline Typography', 'kumar-jewelers'),
                'selector' => '{{WRAPPER}} .kumar-footer-brand-tagline',
                'fields_options' => [
                    'font_family' => ['default' => 'Cormorant Garamond'],
                    'font_size' => ['default' => ['size' => 18, 'unit' => 'px']],
                    'font_style' => ['default' => 'italic'],
                ],
            ]
        );
        
        $this->add_control(
            'brand_description_color',
            [
                'label' => __('Description Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFE5D9',
                'selectors' => [
                    '{{WRAPPER}} .kumar-footer-description' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->end_controls_section();
        
        // Style: Social Media
        $this->start_controls_section(
            'style_social',
            [
                'label' => __('Social Media', 'kumar-jewelers'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'social_heading_color',
            [
                'label' => __('Heading Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#C9A961',
                'selectors' => [
                    '{{WRAPPER}} .kumar-social-section h5' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'social_icon_bg',
            [
                'label' => __('Icon Background', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'rgba(255, 255, 255, 0.05)',
                'selectors' => [
                    '{{WRAPPER}} .kumar-social-links a' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'social_icon_border',
            [
                'label' => __('Icon Border', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'rgba(212, 175, 55, 0.3)',
                'selectors' => [
                    '{{WRAPPER}} .kumar-social-links a' => 'border-color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'social_icon_color',
            [
                'label' => __('Icon Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#D4AF37',
                'selectors' => [
                    '{{WRAPPER}} .kumar-social-links a, {{WRAPPER}} .kumar-social-links a i, {{WRAPPER}} .kumar-social-links a svg' => 'color: {{VALUE}}; fill: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'social_icon_hover_color',
            [
                'label' => __('Icon Hover Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#1a0f0a',
                'selectors' => [
                    '{{WRAPPER}} .kumar-social-links a:hover, {{WRAPPER}} .kumar-social-links a:hover i, {{WRAPPER}} .kumar-social-links a:hover svg' => 'color: {{VALUE}}; fill: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'social_icon_hover_bg',
            [
                'label' => __('Hover Background', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#D4AF37',
                'selectors' => [
                    '{{WRAPPER}} .kumar-social-links a:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'social_icon_hover_border',
            [
                'label' => __('Hover Border Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFD700',
                'selectors' => [
                    '{{WRAPPER}} .kumar-social-links a:hover' => 'border-color: {{VALUE}}',
                ],
            ]
        );
        
        
        $this->add_responsive_control(
            'social_icon_size',
            [
                'label' => __('Icon Size', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => ['min' => 10, 'max' => 50],
                ],
                'default' => ['size' => 16, 'unit' => 'px'],
                'selectors' => [
                    '{{WRAPPER}} .kumar-social-links a' => 'font-size: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .kumar-social-links a svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'social_icon_padding',
            [
                'label' => __('Icon Padding', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => ['min' => 5, 'max' => 50],
                ],
                'default' => ['size' => 12, 'unit' => 'px'],
                'selectors' => [
                    '{{WRAPPER}} .kumar-social-links a' => 'padding: {{SIZE}}{{UNIT}}',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'social_icon_spacing',
            [
                'label' => __('Icon Spacing', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => ['min' => 0, 'max' => 50],
                ],
                'default' => ['size' => 10, 'unit' => 'px'],
                'selectors' => [
                    '{{WRAPPER}} .kumar-social-links' => 'gap: {{SIZE}}{{UNIT}}',
                ],
            ]
        );
        
        $this->add_control(
            'social_icon_border_radius',
            [
                'label' => __('Border Radius', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'default' => [
                    'top' => '50',
                    'right' => '50',
                    'bottom' => '50',
                    'left' => '50',
                    'unit' => '%',
                ],
                'selectors' => [
                    '{{WRAPPER}} .kumar-social-links a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );
        
        
        $this->end_controls_section();
        
        // Style: Collections
        $this->start_controls_section(
            'style_collections',
            [
                'label' => __('Collections', 'kumar-jewelers'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'collections_heading_color',
            [
                'label' => __('Heading Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFD700',
                'selectors' => [
                    '{{WRAPPER}} .kumar-footer-section h4' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'collections_heading_underline',
            [
                'label' => __('Heading Underline', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#D4AF37',
                'selectors' => [
                    '{{WRAPPER}} .kumar-footer-section h4::after' => 'background: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'collections_link_color',
            [
                'label' => __('Link Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFE5D9',
                'selectors' => [
                    '{{WRAPPER}} .kumar-footer-links a' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'collections_link_hover_color',
            [
                'label' => __('Link Hover Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFD700',
                'selectors' => [
                    '{{WRAPPER}} .kumar-footer-links a:hover' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->end_controls_section();
        
        // Style: Services
        $this->start_controls_section(
            'style_services',
            [
                'label' => __('Services', 'kumar-jewelers'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'services_link_color',
            [
                'label' => __('Link Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFE5D9',
                'selectors' => [
                    '{{WRAPPER}} .kumar-footer-section.kumar-services-section .kumar-footer-links a' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'services_link_hover_color',
            [
                'label' => __('Link Hover Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFD700',
                'selectors' => [
                    '{{WRAPPER}} .kumar-footer-section.kumar-services-section .kumar-footer-links a:hover' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->end_controls_section();
        
        // Style: Footer Bottom
        $this->start_controls_section(
            'style_footer_bottom',
            [
                'label' => __('Footer Bottom', 'kumar-jewelers'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'copyright_color',
            [
                'label' => __('Copyright Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFE5D9',
                'selectors' => [
                    '{{WRAPPER}} .kumar-copyright' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'bottom_links_color',
            [
                'label' => __('Links Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#C9A961',
                'selectors' => [
                    '{{WRAPPER}} .kumar-footer-bottom-links a' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'bottom_links_hover_color',
            [
                'label' => __('Links Hover Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFD700',
                'selectors' => [
                    '{{WRAPPER}} .kumar-footer-bottom-links a:hover' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'made_with_love_color',
            [
                'label' => __('Made With Love Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#8B7355',
                'selectors' => [
                    '{{WRAPPER}} .kumar-made-with-love' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->end_controls_section();
    }
    
    protected function render() {
        $settings = $this->get_settings_for_display();
        
        $pattern_style = '';
        if ($settings['enable_pattern_overlay'] === 'yes' && !empty($settings['pattern_overlay_image']['url'])) {
            $pattern_style = 'style="background-image: url(' . esc_url($settings['pattern_overlay_image']['url']) . ');"';
        }
        
        $animation_class = ($settings['enable_scroll_animations'] === 'yes') ? 'kumar-animate-on-scroll' : '';
        ?>
        <footer class="kumar-footer <?php echo esc_attr($animation_class); ?>" 
                data-animation-duration="<?php echo esc_attr($settings['animation_duration']); ?>" 
                data-animation-delay="<?php echo esc_attr($settings['animation_delay']); ?>">
            <div class="kumar-footer-ornament">💎</div>
            
            <div class="kumar-footer-container">
                <!-- HERO SECTION: Our Locations -->
                <div class="kumar-locations-hero" <?php echo $pattern_style; ?>>
                    <div class="kumar-locations-content">
                        <h2 class="kumar-fade-in-up"><?php echo esc_html($settings['locations_heading']); ?></h2>
                        <p class="kumar-locations-hero-desc kumar-fade-in-up"><?php echo esc_html($settings['locations_description']); ?></p>
                        
                        <div class="kumar-store-cards">
                            <?php foreach ($settings['store_locations'] as $index => $location) : ?>
                                <div class="kumar-store-card kumar-fade-in-up" data-index="<?php echo esc_attr($index); ?>">
                                    <div class="kumar-store-name"><?php echo esc_html($location['store_name']); ?></div>
                                    <div class="kumar-store-info">
                                        <?php echo wp_kses_post($location['store_address']); ?>
                                    </div>
                                    <a href="<?php echo esc_url($location['store_phone_link']); ?>" class="kumar-store-phone">
                                        ☎ <?php echo esc_html($location['store_phone']); ?>
                                    </a>
                                    <?php if (!empty($location['store_email'])) : ?>
                                        <a href="mailto:<?php echo esc_attr($location['store_email']); ?>" class="kumar-store-email">
                                            ✉ <?php echo esc_html($location['store_email']); ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- MAIN FOOTER GRID: Brand | Collections | Services | Newsletter -->
                <div class="kumar-footer-main">
                    <!-- Brand Section -->
                    <div class="kumar-footer-brand kumar-fade-in-up" data-index="0">
                        <?php if ($settings['logo_type'] === 'image') : ?>
                            <img src="<?php echo esc_url($settings['logo_image']['url']); ?>" 
                                 alt="<?php echo esc_attr(get_bloginfo('name')); ?>" 
                                 class="kumar-footer-logo-image">
                        <?php else : ?>
                            <div class="kumar-footer-logo" style="background: linear-gradient(135deg, <?php echo esc_attr($settings['brand_logo_color_start']); ?> 0%, <?php echo esc_attr($settings['brand_logo_color_end']); ?> 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                                <?php echo esc_html($settings['logo_text']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <p class="kumar-footer-brand-tagline">
                            <?php echo esc_html($settings['brand_tagline']); ?>
                        </p>
                        <p class="kumar-footer-description">
                            <?php echo esc_html($settings['brand_description']); ?>
                        </p>
                        
                        <div class="kumar-social-section">
                            <h5><?php echo esc_html($settings['social_heading']); ?></h5>
                            <div class="kumar-social-links">
                                <?php foreach ($settings['social_links'] as $social) : ?>
                                    <a href="<?php echo esc_url($social['social_link']['url']); ?>"
                                       aria-label="<?php echo esc_attr($social['social_label']); ?>"
                                       <?php if ($social['social_link']['is_external']) echo 'target="_blank"'; ?>
                                       <?php if ($social['social_link']['nofollow']) echo 'rel="nofollow"'; ?>>
                                        <?php \Elementor\Icons_Manager::render_icon($social['social_icon'], ['aria-hidden' => 'true']); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Collections -->
                    <div class="kumar-footer-section kumar-fade-in-up" data-index="1">
                        <h4><?php echo esc_html($settings['collections_heading']); ?></h4>
                        <ul class="kumar-footer-links">
                            <?php foreach ($settings['collections_links'] as $link) : ?>
                                <li>
                                    <a href="<?php echo esc_url($link['collection_link']['url']); ?>"
                                       <?php if ($link['collection_link']['is_external']) echo 'target="_blank"'; ?>
                                       <?php if ($link['collection_link']['nofollow']) echo 'rel="nofollow"'; ?>>
                                        <?php echo esc_html($link['collection_text']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    
                    <!-- Services -->
                    <div class="kumar-footer-section kumar-services-section kumar-fade-in-up" data-index="2">
                        <h4><?php echo esc_html($settings['services_heading']); ?></h4>
                        <ul class="kumar-footer-links">
                            <?php foreach ($settings['services_links'] as $link) : ?>
                                <li>
                                    <a href="<?php echo esc_url($link['service_link']['url']); ?>"
                                       <?php if ($link['service_link']['is_external']) echo 'target="_blank"'; ?>
                                       <?php if ($link['service_link']['nofollow']) echo 'rel="nofollow"'; ?>>
                                        <?php echo esc_html($link['service_text']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    
                    <!-- Newsletter (4th Column) -->
                    <?php if ($settings['show_newsletter'] === 'yes') : ?>
                        <div class="kumar-footer-section kumar-newsletter kumar-fade-in-up" data-index="3">
                            <h4><?php echo esc_html($settings['newsletter_heading']); ?></h4>
                            <p><?php echo esc_html($settings['newsletter_text']); ?></p>
                            <form class="kumar-newsletter-form">
                                <input type="email" 
                                       class="kumar-newsletter-input" 
                                       placeholder="<?php echo esc_attr($settings['newsletter_placeholder']); ?>" 
                                       required>
                                <button type="submit" class="kumar-newsletter-button">
                                    <?php echo esc_html($settings['newsletter_button_text']); ?>
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Footer Bottom -->
                <div class="kumar-footer-bottom" style="border-top: 1px solid <?php echo esc_attr($settings['border_color']); ?>;">
                    <div class="kumar-copyright">
                        <?php echo wp_kses_post($settings['copyright_text']); ?>
                    </div>
                    <ul class="kumar-footer-bottom-links">
                        <?php foreach ($settings['bottom_links'] as $link) : ?>
                            <li>
                                <a href="<?php echo esc_url($link['link_url']['url']); ?>"
                                   <?php if ($link['link_url']['is_external']) echo 'target="_blank"'; ?>
                                   <?php if ($link['link_url']['nofollow']) echo 'rel="nofollow"'; ?>>
                                    <?php echo esc_html($link['link_text']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="kumar-made-with-love">
                        <?php echo wp_kses_post($settings['made_with_love_text']); ?>
                    </div>
                </div>
            </div>
        </footer>
        
        <style>
        /* Footer Base */
        .kumar-footer {
            position: relative;
            overflow: hidden;
        }
        
        .kumar-footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, #D4AF37, #FFD700, #D4AF37, transparent);
            opacity: 0.6;
        }
        
        .kumar-footer-ornament {
            position: absolute;
            top: -30px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, rgba(139, 21, 56, 0.95), rgba(107, 18, 39, 0.95));
            border: 3px solid #D4AF37;
            border-radius: inherit;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
            z-index: 10;
        }
        
        .kumar-footer-container {
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
        }
        
        /* HERO SECTION: Locations */
        .kumar-locations-hero {
            position: relative;
            text-align: center;
            padding: 60px 0 80px;
            border-bottom: 1px solid <?php echo esc_attr($settings['border_color']); ?>;
            margin-bottom: 60px;
        }
        
        .kumar-locations-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 60px;
        }
        
        
        .kumar-locations-hero::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-size: cover;
            background-position: center;
            background-repeat: repeat;
            pointer-events: none;
            z-index: 1;
        }
        
        .kumar-locations-content {
            position: relative;
            z-index: 2;
        }
        
        .kumar-locations-hero h2 {
            margin-bottom: 15px;
            text-shadow: 0 2px 20px rgba(139, 21, 56, 0.2);
        }
        
        .kumar-locations-hero-desc {
            font-family: 'Jost', sans-serif;
            font-size: 1.1rem;
            margin-bottom: 50px;
            opacity: 0.9;
        }
        
        .kumar-store-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .kumar-store-card {
            backdrop-filter: blur(10px);
            padding: 35px;
            border-radius: 20px;
            border: 2px solid;
            transition: all 0.4s ease;
            text-align: left;
        }
        
        .kumar-store-card:hover {
            transform: translateY(-10px);
        }
        
        .kumar-store-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .kumar-store-info {
            font-family: 'Jost', sans-serif;
            font-size: 1rem;
            line-height: 1.8;
            margin-bottom: 15px;
        }
        
        .kumar-store-phone,
        .kumar-store-email {
            display: inline-block;
            font-family: 'Jost', sans-serif;
            font-weight: 600;
            text-decoration: none;
            margin-right: 20px;
            margin-top: 10px;
            transition: all 0.3s ease;
        }
        
        .kumar-store-phone:hover,
        .kumar-store-email:hover {
            letter-spacing: 1px;
        }
        
        /* Animation Classes */
        .kumar-fade-in-up {
            opacity: 0;
            transform: translateY(30px);
        }
        
        .kumar-animate-on-scroll .kumar-fade-in-up.kumar-animated {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* MAIN FOOTER GRID */
        .kumar-footer-main {
            display: grid;
            grid-template-columns: 1.5fr 1fr 1fr 1.2fr;
            gap: 60px;
            padding-bottom: 60px;
        }
        
        /* Brand Section */
        .kumar-footer-brand {
            padding-right: 20px;
        }
        
        .kumar-footer-logo-image {
            display: block;
            height: auto;
            margin-bottom: 20px;
        }
        
        .kumar-footer-logo {
            font-family: 'Cormorant Garamond', serif;
            margin-bottom: 20px;
            letter-spacing: 2px;
            line-height: 1.1;
        }
        
        .kumar-footer-brand-tagline {
            font-family: 'Cormorant Garamond', serif;
            font-style: italic;
            margin-bottom: 25px;
            line-height: 1.5;
        }
        
        .kumar-footer-description {
            font-family: 'Jost', sans-serif;
            font-size: 0.95rem;
            line-height: 1.8;
            margin-bottom: 30px;
        }
        
        /* Social Section */
        .kumar-social-section h5 {
            font-family: 'Jost', sans-serif;
            font-size: 0.85rem;
            font-weight: 500;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 15px;
        }
        
        .kumar-social-links {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        
        .kumar-social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.4s ease;
            position: relative;
            backdrop-filter: blur(10px);
            border: 2px solid;
        }
        
        .kumar-social-links a i,
        .kumar-social-links a svg {
            display: block;
        }
        
        .kumar-social-links a::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #D4AF37, #FFD700);
            border-radius: inherit;
            transition: transform 0.4s ease;
            z-index: -1;
        }
        
        .kumar-social-links a:hover::before {
            transform: translate(-50%, -50%) scale(1);
        }
        
        .kumar-social-links a:hover {
            border-color: #FFD700;
            transform: translateY(-5px);
        }
        
        /* Footer Sections (Collections & Services) */
        .kumar-footer-section h4 {
            font-family: 'Cormorant Garamond', serif;
            margin-bottom: 25px;
            position: relative;
            padding-bottom: 12px;
        }
        
        .kumar-footer-section h4::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 2px;
        }
        
        .kumar-footer-links {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin: 0;
            padding: 0;
        }
        
        .kumar-footer-links a {
            font-family: 'Jost', sans-serif;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .kumar-footer-links a::before {
            content: '→';
            opacity: 0;
            transform: translateX(-10px);
            transition: all 0.3s ease;
        }
        
        .kumar-footer-links a:hover {
            padding-left: 15px;
        }
        
        .kumar-footer-links a:hover::before {
            opacity: 1;
            transform: translateX(0);
        }
        
        /* Newsletter in Grid */
        .kumar-newsletter h4 {
            font-family: 'Cormorant Garamond', serif;
            margin-bottom: 15px;
        }
        
        .kumar-newsletter p {
            font-family: 'Jost', sans-serif;
            font-size: 0.9rem;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        
        .kumar-newsletter-form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .kumar-newsletter-input {
            padding: 14px 20px;
            backdrop-filter: blur(10px);
            border: 2px solid;
            border-radius: 8px;
            font-family: 'Jost', sans-serif;
            font-size: 0.9rem;
            color: #FFF8E7;
            outline: none;
            transition: all 0.3s ease;
        }
        
        .kumar-newsletter-input::placeholder {
            color: rgba(255, 232, 217, 0.5);
        }
        
        .kumar-newsletter-input:focus {
            border-color: #D4AF37;
            background: rgba(255, 255, 255, 0.12);
        }
        
        .kumar-newsletter-button {
            padding: 14px 25px;
            border: none;
            border-radius: 8px;
            font-family: 'Jost', sans-serif;
            font-size: 0.9rem;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        }
        
        .kumar-newsletter-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(212, 175, 55, 0.5);
        }
        
        /* Footer Bottom */
        .kumar-footer-bottom {
            padding: 40px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .kumar-copyright {
            font-family: 'Jost', sans-serif;
            font-size: 0.9rem;
        }
        
        .kumar-footer-bottom-links {
            display: flex;
            gap: 30px;
            list-style: none;
            margin: 0;
            padding: 0;
            flex-wrap: wrap;
        }
        
        .kumar-footer-bottom-links a {
            font-family: 'Jost', sans-serif;
            font-size: 0.85rem;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .kumar-footer-bottom-links a::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 0;
            height: 1px;
            background: #D4AF37;
            transition: width 0.3s ease;
        }
        
        .kumar-footer-bottom-links a:hover::after {
            width: 100%;
        }
        
        .kumar-made-with-love {
            font-family: 'Jost', sans-serif;
            font-size: 0.85rem;
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .kumar-footer-main {
                grid-template-columns: 1fr 1fr;
                gap: 50px;
            }
            
            .kumar-store-cards {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .kumar-locations-container {
                padding-left: 30px;
                padding-right: 30px;
            }
            
            .kumar-footer-container {
                padding: 50px 30px 0 !important;
            }
            
            .kumar-locations-hero {
                padding: 40px 0 60px;
            }
            
            .kumar-locations-hero h2 {
                font-size: 2.5rem;
            }
            
            .kumar-store-cards {
                gap: 20px;
            }
            
            .kumar-store-card {
                padding: 25px;
            }
            
            .kumar-footer-main {
                grid-template-columns: 1fr;
                gap: 40px;
                padding-bottom: 40px;
            }
            
            .kumar-footer-brand {
                padding-right: 0;
            }
            
            .kumar-footer-bottom {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
            
            .kumar-footer-bottom-links {
                justify-content: center;
            }
        }
        
        /* Newsletter Message Styling */
        .kumar-newsletter-message {
            margin-top: 15px;
            padding: 12px;
            border-radius: 8px;
            font-family: 'Jost', sans-serif;
            font-size: 0.95rem;
            display: none;
        }
        
        .kumar-newsletter-message.success {
            display: block;
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .kumar-newsletter-message.error {
            display: block;
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* Mobile Accordion Toggle Icon */
        .kumar-mobile-menu-toggle {
            display: none;
            font-size: 0.9rem;
            transition: transform 0.3s ease;
        }
        
        .kumar-mobile-menu-toggle.active {
            transform: rotate(180deg);
        }
        
        /* Mobile Responsive Styles */
        @media (max-width: 768px) {
            /* Center logo and tagline on mobile */
            .kumar-footer-brand {
                text-align: center;
            }
            
            .kumar-footer-logo-image {
                margin-left: auto;
                margin-right: auto;
                display: block;
            }
            
            /* Center social icons on mobile */
            .kumar-social-section {
                text-align: center;
            }
            
            .kumar-social-links {
                justify-content: center;
            }
            
            /* Mobile Accordion for menu columns */
            .kumar-footer-column h3 {
                cursor: pointer;
                user-select: none;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            
            .kumar-mobile-menu-toggle {
                display: inline-block;
            }
            
            .kumar-footer-links {
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.4s ease;
            }
            
            .kumar-footer-links.active {
                max-height: 500px;
                margin-top: 20px;
            }
            
            /* Center all menu items on mobile */
            .kumar-footer-column {
                text-align: center;
            }
            
            .kumar-footer-links li {
                text-align: center;
            }
            
            .kumar-footer-links a {
                display: inline-block;
                text-align: center;
            }
            
            /* Remove hover arrow on mobile for centered layout */
            .kumar-footer-links a::before {
                display: none;
            }
            
            .kumar-footer-links a:hover {
                padding-left: 0;
            }
        }


        /* Mobile Accordion - Fixed for Footer Sections */
        @media (max-width: 768px) {
            /* Center the entire footer section */
            .kumar-footer-section {
                text-align: center;
            }

            /* Make entire heading clickable with arrow */
            .kumar-footer-section h4 {
                position: relative;
                cursor: pointer;
                user-select: none;
                padding: 0 40px 12px 40px !important;
                width: 100%;
                text-align: center !important;
                display: block !important;
                margin-bottom: 0 !important;
            }

            /* Completely override desktop underline ::after and replace with arrow */
            .kumar-footer-section h4::after {
                content: '▼' !important;
                position: absolute !important;
                right: 0 !important;
                top: 0 !important;
                left: auto !important;
                bottom: auto !important;
                width: auto !important;
                height: auto !important;
                transform: none !important;
                font-size: 1.2em !important;
                color: #D4AF37 !important;
                transition: transform 0.3s ease !important;
                background: none !important;
            }

            /* Rotate arrow when active */
            .kumar-footer-section h4.active::after {
                transform: rotate(180deg) !important;
            }

            /* Hide footer links by default on mobile */
            .kumar-footer-section .kumar-footer-links {
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.4s ease;
                margin-top: 0;
            }

            /* Show footer links when active */
            .kumar-footer-section .kumar-footer-links.active {
                max-height: 500px;
                margin-top: 20px;
            }

            /* Hide newsletter content (paragraph and form) by default on mobile */
            .kumar-newsletter p,
            .kumar-newsletter .kumar-newsletter-form {
                max-height: 0;
                overflow: hidden;
                opacity: 0;
                transition: max-height 0.4s ease, opacity 0.3s ease, margin 0.4s ease;
                margin: 0;
            }

            /* Show newsletter content when active */
            .kumar-newsletter p.active,
            .kumar-newsletter .kumar-newsletter-form.active {
                max-height: 300px;
                opacity: 1;
                margin-bottom: 20px;
            }

            .kumar-newsletter p.active {
                margin-bottom: 20px;
            }
        }
        </style>
        
        <script>
        (function() {
            // Newsletter AJAX submission
            const newsletterForm = document.querySelector('.kumar-newsletter-form');
            if (newsletterForm) {
                newsletterForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const emailInput = this.querySelector('.kumar-newsletter-input');
                    const button = this.querySelector('.kumar-newsletter-button');
                    const messageDiv = document.querySelector('.kumar-newsletter-message');
                    const email = emailInput.value;
                    const widgetId = this.dataset.widgetId;
                    
                    button.disabled = true;
                    const originalText = button.textContent;
                    button.textContent = 'Subscribing...';
                    
                    fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: new URLSearchParams({
                            action: 'kumar_newsletter_subscribe',
                            email: email,
                            widget_id: widgetId,
                            nonce: '<?php echo wp_create_nonce("kumar_newsletter_nonce"); ?>'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        messageDiv.textContent = data.data.message;
                        messageDiv.className = 'kumar-newsletter-message ' + (data.success ? 'success' : 'error');
                        if (data.success) emailInput.value = '';
                        button.disabled = false;
                        button.textContent = originalText;
                    })
                    .catch(error => {
                        messageDiv.textContent = 'An error occurred. Please try again.';
                        messageDiv.className = 'kumar-newsletter-message error';
                        button.disabled = false;
                        button.textContent = originalText;
                    });
                });
            }
            
            // Mobile accordion - Fixed for footer sections
            function initAccordion() {
                if (window.innerWidth <= 768) {
                    // Target all h4 headings in footer sections (Collections, Services, Newsletter)
                    document.querySelectorAll('.kumar-footer-section h4').forEach(title => {
                        // Make entire heading clickable
                        title.style.cursor = 'pointer';

                        title.addEventListener('click', function() {
                            // Toggle active class on heading
                            this.classList.toggle('active');

                            // Find the next sibling which should be the links container or form
                            const nextElement = this.nextElementSibling;

                            // Toggle active class on the links/content
                            if (nextElement) {
                                if (nextElement.classList.contains('kumar-footer-links')) {
                                    nextElement.classList.toggle('active');
                                } else if (nextElement.tagName === 'P' || nextElement.tagName === 'FORM') {
                                    // For newsletter section, toggle the paragraph and form
                                    let sibling = nextElement;
                                    while (sibling) {
                                        if (sibling.classList && sibling.classList.contains('kumar-footer-links')) {
                                            break;
                                        }
                                        if (sibling.classList) {
                                            sibling.classList.toggle('active');
                                        }
                                        sibling = sibling.nextElementSibling;
                                        if (sibling && sibling.tagName === 'H4') break;
                                    }
                                }
                            }
                        });
                    });
                }
            }
            initAccordion();

            // Reset accordion state on desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    document.querySelectorAll('.kumar-footer-links').forEach(links => {
                        links.classList.remove('active');
                        links.style.maxHeight = 'none';
                    });
                    document.querySelectorAll('.kumar-footer-section h4').forEach(title => {
                        title.classList.remove('active');
                    });
                } else {
                    // Re-initialize on mobile
                    initAccordion();
                }
            });
            
            
            // Scroll Animation System
            const footer = document.querySelector('.kumar-footer');
            if (footer && footer.classList.contains('kumar-animate-on-scroll')) {
                const animationDuration = parseInt(footer.dataset.animationDuration) || 800;
                const animationDelay = parseInt(footer.dataset.animationDelay) || 150;
                
                const observerOptions = {
                    root: null,
                    rootMargin: '0px',
                    threshold: 0.1
                };
                
                const observer = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            const element = entry.target;
                            const index = parseInt(element.dataset.index) || 0;
                            const delay = index * animationDelay;
                            
                            element.style.transition = 'opacity ' + animationDuration + 'ms ease ' + delay + 'ms, transform ' + animationDuration + 'ms ease ' + delay + 'ms';
                            
                            setTimeout(function() {
                                element.classList.add('kumar-animated');
                            }, 50);
                            
                            observer.unobserve(element);
                        }
                    });
                }, observerOptions);
                
                // Observe all animated elements
                const animatedElements = footer.querySelectorAll('.kumar-fade-in-up');
                animatedElements.forEach(function(element) {
                    observer.observe(element);
                });
            }
        })();
        </script>
        <?php
    }
}
?>