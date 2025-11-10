<?php
/**
 * Kumar Header Widget - Complete Version
 */

if (!defined('ABSPATH')) {
    exit;
}

class Kumar_Header_Widget extends \Elementor\Widget_Base {
    
    public function get_name() {
        return 'kumar_header';
    }
    
    public function get_title() {
        return __('Kumar Header', 'kumar-jewelers');
    }
    
    public function get_icon() {
        return 'eicon-header';
    }
    
    public function get_categories() {
        return ['general'];
    }
    
    public function get_keywords() {
        return ['header', 'navigation', 'menu', 'kumar', 'jewelers'];
    }
    
    protected function register_controls() {
        
        // ========== LOGO SECTION ==========
        $this->start_controls_section(
            'logo_section',
            [
                'label' => __('Logo', 'kumar-jewelers'),
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
                'default' => 'text',
            ]
        );
        
        // Text Logo Controls
        $this->add_control(
            'logo_text',
            [
                'label' => __('Logo Text', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'KUMAR JEWELERS',
                'dynamic' => ['active' => true],
                'condition' => ['logo_type' => 'text'],
            ]
        );
        
        // Image Logo Controls
        $this->add_control(
            'logo_image',
            [
                'label' => __('Choose Logo Image', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
                'condition' => ['logo_type' => 'image'],
            ]
        );
        
        $this->add_responsive_control(
            'logo_image_width',
            [
                'label' => __('Logo Width', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => ['min' => 50, 'max' => 500, 'step' => 5],
                    '%' => ['min' => 10, 'max' => 100],
                ],
                'default' => ['size' => 180, 'unit' => 'px'],
                'selectors' => [
                    '{{WRAPPER}} .kumar-logo-image' => 'width: {{SIZE}}{{UNIT}}',
                ],
                'condition' => ['logo_type' => 'image'],
            ]
        );
        
        // Tagline Controls
        $this->add_control(
            'show_tagline',
            [
                'label' => __('Show Tagline', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'kumar-jewelers'),
                'label_off' => __('No', 'kumar-jewelers'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'tagline_text',
            [
                'label' => __('Tagline', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Timeless Elegance',
                'dynamic' => ['active' => true],
                'condition' => ['show_tagline' => 'yes'],
            ]
        );
        
        $this->add_control(
            'logo_link',
            [
                'label' => __('Logo Link', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::URL,
                'default' => ['url' => home_url('/')],
                'placeholder' => home_url('/'),
            ]
        );
        
        $this->end_controls_section();
        
        // ========== NAVIGATION MENU ==========
        $this->start_controls_section(
            'menu_section',
            [
                'label' => __('Navigation Menu', 'kumar-jewelers'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'menu_source',
            [
                'label' => __('Menu Source', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'wordpress' => __('WordPress Menu', 'kumar-jewelers'),
                    'custom' => __('Custom Menu', 'kumar-jewelers'),
                ],
                'default' => 'custom',
            ]
        );
        
        // WordPress Menu
        $menus = wp_get_nav_menus();
        $menu_options = [];
        foreach ($menus as $menu) {
            $menu_options[$menu->term_id] = $menu->name;
        }
        
        $this->add_control(
            'wordpress_menu',
            [
                'label' => __('Select Menu', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $menu_options,
                'condition' => ['menu_source' => 'wordpress'],
                'description' => __('Select a menu from Appearance > Menus', 'kumar-jewelers'),
            ]
        );
        
        // Custom Menu
        $repeater = new \Elementor\Repeater();
        
        $repeater->add_control(
            'menu_text',
            [
                'label' => __('Menu Text', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Menu Item', 'kumar-jewelers'),
            ]
        );
        
        $repeater->add_control(
            'menu_link',
            [
                'label' => __('Link', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::URL,
                'default' => ['url' => '#'],
            ]
        );
        
        $this->add_control(
            'menu_items',
            [
                'label' => __('Menu Items', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    ['menu_text' => 'Home', 'menu_link' => ['url' => '#home']],
                    ['menu_text' => 'Categories', 'menu_link' => ['url' => '#categories']],
                    ['menu_text' => 'Collections', 'menu_link' => ['url' => '#collections']],
                    ['menu_text' => 'About', 'menu_link' => ['url' => '#about']],
                    ['menu_text' => 'Contact', 'menu_link' => ['url' => '#contact']],
                ],
                'title_field' => '{{{ menu_text }}}',
                'condition' => ['menu_source' => 'custom'],
            ]
        );
        
        $this->end_controls_section();
        
        // ========== CTA BUTTON ==========
        $this->start_controls_section(
            'button_section',
            [
                'label' => __('CTA Button', 'kumar-jewelers'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'show_button',
            [
                'label' => __('Show Button', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'kumar-jewelers'),
                'label_off' => __('No', 'kumar-jewelers'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        
        $this->add_control(
            'button_text',
            [
                'label' => __('Button Text', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Call Us',
                'condition' => ['show_button' => 'yes'],
            ]
        );
        
        $this->add_control(
            'button_link',
            [
                'label' => __('Button Link', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::URL,
                'default' => ['url' => 'tel:5107131040'],
                'condition' => ['show_button' => 'yes'],
            ]
        );
        
        $this->add_control(
            'button_style',
            [
                'label' => __('Button Style', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'golden-shine' => __('Golden Shine', 'kumar-jewelers'),
                    'elegant-border' => __('Elegant Border', 'kumar-jewelers'),
                    'diamond-cut' => __('Diamond Cut', 'kumar-jewelers'),
                    'royal-gradient' => __('Royal Gradient', 'kumar-jewelers'),
                    'ornate-frame' => __('Ornate Frame', 'kumar-jewelers'),
                    'glowing-edge' => __('Glowing Edge', 'kumar-jewelers'),
                    'split-reveal' => __('Split Reveal', 'kumar-jewelers'),
                    'luxury-underline' => __('Luxury Underline', 'kumar-jewelers'),
                    'premium-glow' => __('Premium Glow', 'kumar-jewelers'),
                    'embossed' => __('Embossed Luxury', 'kumar-jewelers'),
                    'minimal-slide' => __('Minimal Slide', 'kumar-jewelers'),
                    'heritage' => __('Heritage Border', 'kumar-jewelers'),
                ],
                'default' => 'golden-shine',
                'condition' => ['show_button' => 'yes'],
            ]
        );
        
        $this->end_controls_section();
        
        // ========== STYLE: HEADER CONTAINER ==========
        $this->start_controls_section(
            'style_container',
            [
                'label' => __('Header Container', 'kumar-jewelers'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'header_background',
            [
                'label' => __('Background Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#0a0a0a',
                'selectors' => [
                    '{{WRAPPER}} .kumar-header' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'header_scrolled_background',
            [
                'label' => __('Scrolled Background', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'rgba(26, 15, 10, 0.95)',
                'selectors' => [
                    '{{WRAPPER}} .kumar-header.scrolled' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'header_padding',
            [
                'label' => __('Padding', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'default' => [
                    'top' => '25',
                    'right' => '60',
                    'bottom' => '25',
                    'left' => '60',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .kumar-header-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );
        
        $this->add_control(
    'enable_sticky',
    [
        'label' => __('Sticky Header', 'kumar-jewelers'),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => __('Yes', 'kumar-jewelers'),
        'label_off' => __('No', 'kumar-jewelers'),
        'return_value' => 'yes',
        'default' => 'yes',
        'description' => __('Enable or disable sticky header on scroll', 'kumar-jewelers'),
    ]
);
        
        $this->end_controls_section();
        
        // ========== STYLE: LOGO ==========
        $this->start_controls_section(
            'style_logo',
            [
                'label' => __('Logo', 'kumar-jewelers'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'logo_typography',
                'label' => __('Logo Typography', 'kumar-jewelers'),
                'selector' => '{{WRAPPER}} .kumar-logo',
                'fields_options' => [
                    'font_family' => ['default' => 'Cormorant Garamond'],
                    'font_size' => ['default' => ['size' => 32, 'unit' => 'px']],
                    'font_weight' => ['default' => '700'],
                ],
                'condition' => ['logo_type' => 'text'],
            ]
        );
        
        $this->add_control(
            'logo_color_start',
            [
                'label' => __('Logo Color (Start)', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFD700',
                'condition' => ['logo_type' => 'text'],
            ]
        );
        
        $this->add_control(
            'logo_color_end',
            [
                'label' => __('Logo Color (End)', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#D4AF37',
                'condition' => ['logo_type' => 'text'],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'tagline_typography',
                'label' => __('Tagline Typography', 'kumar-jewelers'),
                'selector' => '{{WRAPPER}} .kumar-tagline',
                'fields_options' => [
                    'font_family' => ['default' => 'Jost'],
                    'font_size' => ['default' => ['size' => 11, 'unit' => 'px']],
                    'font_weight' => ['default' => '400'],
                ],
                'condition' => ['show_tagline' => 'yes'],
            ]
        );
        
        $this->add_control(
            'tagline_color',
            [
                'label' => __('Tagline Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#C9A961',
                'selectors' => [
                    '{{WRAPPER}} .kumar-tagline' => 'color: {{VALUE}} !important',
                ],
                'condition' => ['show_tagline' => 'yes'],
            ]
        );
        
        $this->end_controls_section();
        
        // ========== STYLE: NAVIGATION ==========
        $this->start_controls_section(
            'style_nav',
            [
                'label' => __('Navigation', 'kumar-jewelers'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'nav_typography',
                'selector' => '{{WRAPPER}} .kumar-nav-links a',
                'fields_options' => [
                    'font_family' => ['default' => 'Jost'],
                    'font_size' => ['default' => ['size' => 15, 'unit' => 'px']],
                    'font_weight' => ['default' => '400'],
                ],
            ]
        );
        
        $this->start_controls_tabs('nav_style_tabs');
        
        $this->start_controls_tab(
            'nav_normal',
            ['label' => __('Normal', 'kumar-jewelers')]
        );
        
        $this->add_control(
            'nav_color',
            [
                'label' => __('Text Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFF8E7',
                'selectors' => [
                    '{{WRAPPER}} .kumar-nav-links a' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->end_controls_tab();
        
        $this->start_controls_tab(
            'nav_hover',
            ['label' => __('Hover', 'kumar-jewelers')]
        );
        
        $this->add_control(
            'nav_hover_color',
            [
                'label' => __('Text Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFD700',
                'selectors' => [
                    '{{WRAPPER}} .kumar-nav-links a:hover' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'nav_underline_color',
            [
                'label' => __('Underline Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#D4AF37',
                'selectors' => [
                    '{{WRAPPER}} .kumar-nav-links a::after' => 'background: linear-gradient(90deg, transparent, {{VALUE}}, transparent)',
                ],
            ]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->add_responsive_control(
            'nav_spacing',
            [
                'label' => __('Items Spacing', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => ['px' => ['min' => 10, 'max' => 100]],
                'default' => ['size' => 45, 'unit' => 'px'],
                'selectors' => [
                    '{{WRAPPER}} .kumar-nav-links' => 'gap: {{SIZE}}{{UNIT}}',
                ],
                'separator' => 'before',
            ]
        );
        
        $this->end_controls_section();
        
        // ========== STYLE: MOBILE MENU ==========
        $this->start_controls_section(
            'style_mobile',
            [
                'label' => __('Mobile Menu', 'kumar-jewelers'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'mobile_menu_bg',
            [
                'label' => __('Background Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'rgba(26, 15, 10, 0.98)',
                'selectors' => [
                    '{{WRAPPER}} .kumar-mobile-menu' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'mobile_backdrop_color',
            [
                'label' => __('Backdrop Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'rgba(0, 0, 0, 0.7)',
            ]
        );
        
        $this->add_control(
            'mobile_link_color',
            [
                'label' => __('Link Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFF8E7',
                'selectors' => [
                    '{{WRAPPER}} .kumar-mobile-nav-links a' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'mobile_link_hover_color',
            [
                'label' => __('Link Hover Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFD700',
                'selectors' => [
                    '{{WRAPPER}} .kumar-mobile-nav-links a:hover' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'hamburger_color',
            [
                'label' => __('Hamburger Icon Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#D4AF37',
                'selectors' => [
                    '{{WRAPPER}} .kumar-menu-toggle span' => 'background: {{VALUE}}',
                ],
            ]
        );
        
        $this->end_controls_section();
        
        // ========== STYLE: BUTTON ==========
        $this->start_controls_section(
            'style_button',
            [
                'label' => __('CTA Button', 'kumar-jewelers'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => ['show_button' => 'yes'],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .kumar-cta-button',
                'fields_options' => [
                    'font_family' => ['default' => 'Jost'],
                    'font_size' => ['default' => ['size' => 14, 'unit' => 'px']],
                    'font_weight' => ['default' => '600'],
                ],
            ]
        );
        
        $this->add_responsive_control(
            'button_padding',
            [
                'label' => __('Padding', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px'],
                'default' => [
                    'top' => '12',
                    'right' => '30',
                    'bottom' => '12',
                    'left' => '30',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .kumar-cta-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );
        
        $this->start_controls_tabs('button_style_tabs');
        
        $this->start_controls_tab(
            'button_normal',
            ['label' => __('Normal', 'kumar-jewelers')]
        );
        
        $this->add_control(
            'button_text_color',
            [
                'label' => __('Text Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#1a0f0a',
                'selectors' => [
                    '{{WRAPPER}} .kumar-cta-button' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'button_background',
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .kumar-cta-button',
                'fields_options' => [
                    'background' => ['default' => 'gradient'],
                    'color' => ['default' => '#D4AF37'],
                    'color_b' => ['default' => '#C9A961'],
                    'gradient_angle' => ['default' => ['size' => 135, 'unit' => 'deg']],
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'button_border',
                'selector' => '{{WRAPPER}} .kumar-cta-button',
            ]
        );
        
        $this->add_responsive_control(
            'button_border_radius',
            [
                'label' => __('Border Radius', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'default' => [
                    'top' => '30',
                    'right' => '30',
                    'bottom' => '30',
                    'left' => '30',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .kumar-cta-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_box_shadow',
                'selector' => '{{WRAPPER}} .kumar-cta-button',
            ]
        );
        
        $this->end_controls_tab();
        
        $this->start_controls_tab(
            'button_hover',
            ['label' => __('Hover', 'kumar-jewelers')]
        );
        
        $this->add_control(
            'button_hover_text_color',
            [
                'label' => __('Text Color', 'kumar-jewelers'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .kumar-cta-button:hover' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'button_hover_background',
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .kumar-cta-button:hover',
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'button_hover_border',
                'selector' => '{{WRAPPER}} .kumar-cta-button:hover',
            ]
        );
        
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_hover_box_shadow',
                'selector' => '{{WRAPPER}} .kumar-cta-button:hover',
            ]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->end_controls_section();
    }
    
    protected function render() {
        $settings = $this->get_settings_for_display();
        $widget_id = $this->get_id();
        
        // Get menu items based on source
        $menu_items = [];
        if ($settings['menu_source'] === 'wordpress' && !empty($settings['wordpress_menu'])) {
            $menu_id = $settings['wordpress_menu'];
            $menu_items_wp = wp_get_nav_menu_items($menu_id);
            if ($menu_items_wp) {
                foreach ($menu_items_wp as $item) {
                    $menu_items[] = [
                        'text' => $item->title,
                        'url' => $item->url,
                        'target' => $item->target,
                    ];
                }
            }
        } else {
            foreach ($settings['menu_items'] as $item) {
                $menu_items[] = [
                    'text' => $item['menu_text'],
                    'url' => $item['menu_link']['url'],
                    'target' => $item['menu_link']['is_external'] ? '_blank' : '',
                    'nofollow' => $item['menu_link']['nofollow'] ? 'nofollow' : '',
                ];
            }
        }
        
        ?>
       <header class="kumar-header <?php echo ($settings['enable_sticky'] === 'yes') ? 'kumar-sticky' : 'kumar-static'; ?>" id="kumar-header-<?php echo esc_attr($widget_id); ?>">
            <div class="kumar-header-container">
                <div class="kumar-logo-section">
                    <a href="<?php echo esc_url($settings['logo_link']['url']); ?>"
                       <?php if ($settings['logo_link']['is_external']) echo 'target="_blank"'; ?>
                       <?php if ($settings['logo_link']['nofollow']) echo 'rel="nofollow"'; ?>>
                        
                        <?php if ($settings['logo_type'] === 'text') : ?>
                            <div class="kumar-logo" style="background: linear-gradient(135deg, <?php echo esc_attr($settings['logo_color_start']); ?> 0%, <?php echo esc_attr($settings['logo_color_end']); ?> 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                                <?php echo esc_html($settings['logo_text']); ?>
                            </div>
                        <?php else : ?>
                            <img src="<?php echo esc_url($settings['logo_image']['url']); ?>" 
                                 alt="<?php echo esc_attr(get_bloginfo('name')); ?>" 
                                 class="kumar-logo-image">
                        <?php endif; ?>
                        
                        <?php if ($settings['show_tagline'] === 'yes') : ?>
                            <div class="kumar-tagline">
                                <?php echo esc_html($settings['tagline_text']); ?>
                            </div>
                        <?php endif; ?>
                    </a>
                </div>
                
                <nav class="kumar-nav">
                    <ul class="kumar-nav-links">
                        <?php foreach ($menu_items as $item) : ?>
                            <li>
                                <a href="<?php echo esc_url($item['url']); ?>"
                                   <?php if (!empty($item['target'])) echo 'target="' . esc_attr($item['target']) . '"'; ?>
                                   <?php if (!empty($item['nofollow'])) echo 'rel="' . esc_attr($item['nofollow']) . '"'; ?>>
                                    <?php echo esc_html($item['text']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <?php if ($settings['show_button'] === 'yes') : ?>
                        <a href="<?php echo esc_url($settings['button_link']['url']); ?>"
                           class="kumar-cta-button kumar-btn-<?php echo esc_attr($settings['button_style']); ?>"
                           <?php if ($settings['button_link']['is_external']) echo 'target="_blank"'; ?>
                           <?php if ($settings['button_link']['nofollow']) echo 'rel="nofollow"'; ?>>
                            <?php echo esc_html($settings['button_text']); ?>
                        </a>
                    <?php endif; ?>
                </nav>
                
                <div class="kumar-menu-toggle" id="kumar-menu-toggle-<?php echo esc_attr($widget_id); ?>">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </header>
        
        <!-- Mobile Menu Backdrop -->
        <div class="kumar-mobile-backdrop" id="kumar-backdrop-<?php echo esc_attr($widget_id); ?>" style="background-color: <?php echo esc_attr($settings['mobile_backdrop_color']); ?>"></div>
        
        <!-- Mobile Menu -->
        <div class="kumar-mobile-menu" id="kumar-mobile-menu-<?php echo esc_attr($widget_id); ?>">
            <!-- Mobile Menu Logo -->
            <div class="kumar-mobile-logo">
                <?php if ($settings['logo_type'] === 'text') : ?>
                    <div class="kumar-logo" style="background: linear-gradient(135deg, <?php echo esc_attr($settings['logo_color_start']); ?> 0%, <?php echo esc_attr($settings['logo_color_end']); ?> 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                        <?php echo esc_html($settings['logo_text']); ?>
                    </div>
                <?php else : ?>
                    <img src="<?php echo esc_url($settings['logo_image']['url']); ?>" 
                         alt="<?php echo esc_attr(get_bloginfo('name')); ?>" 
                         class="kumar-logo-image">
                <?php endif; ?>
            </div>
            
            <ul class="kumar-mobile-nav-links">
                <?php foreach ($menu_items as $item) : ?>
                    <li>
                        <a href="<?php echo esc_url($item['url']); ?>"
                           <?php if (!empty($item['target'])) echo 'target="' . esc_attr($item['target']) . '"'; ?>
                           <?php if (!empty($item['nofollow'])) echo 'rel="' . esc_attr($item['nofollow']) . '"'; ?>>
                            <?php echo esc_html($item['text']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            
            <?php if ($settings['show_button'] === 'yes') : ?>
                <div class="kumar-mobile-cta">
                    <a href="<?php echo esc_url($settings['button_link']['url']); ?>"
                       class="kumar-cta-button kumar-btn-<?php echo esc_attr($settings['button_style']); ?>"
                       style="display: block; text-align: center;"
                       <?php if ($settings['button_link']['is_external']) echo 'target="_blank"'; ?>
                       <?php if ($settings['button_link']['nofollow']) echo 'rel="nofollow"'; ?>>
                        <?php echo esc_html($settings['button_text']); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
        <style>
        /* Base Header Styles */
       /* Base Header Styles */
.kumar-header {
    top: 0;
    width: 100%;
    z-index: 999;
    transition: all 0.4s ease;
}

.kumar-header.kumar-sticky {
    position: fixed;
}

.kumar-header.kumar-static {
    position: absolute;
}
        
        .kumar-header.scrolled {
            backdrop-filter: blur(15px);
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.3);
            border-bottom: 1px solid rgba(212, 175, 55, 0.2);
        }
        
        .kumar-header-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .kumar-logo-section {
            display: flex;
            flex-direction: column;
            gap: 5px;
            z-index: 1001;
        }
        
        .kumar-logo-section a {
            text-decoration: none;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .kumar-logo {
            font-family: 'Cormorant Garamond', serif;
            letter-spacing: 2px;
            line-height: 1;
        }
        
        .kumar-logo-image {
            display: block;
            height: auto;
            max-width: 100%;
        }
        
        .kumar-tagline {
            font-family: 'Jost', sans-serif;
            letter-spacing: 3px;
            text-transform: uppercase;
            padding-left: 2px;
        }
        
        .kumar-nav {
            display: flex;
            align-items: center;
            gap: 50px;
        }
        
        .kumar-nav-links {
            display: flex;
            list-style: none;
            align-items: center;
            margin: 0;
            padding: 0;
        }
        
        .kumar-nav-links a {
            font-family: 'Jost', sans-serif;
            text-decoration: none;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            position: relative;
            padding: 5px 0;
        }
        
        .kumar-nav-links a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            transition: width 0.4s ease;
        }
        
        .kumar-nav-links a:hover::after {
            width: 100%;
        }
        
        /* CTA Button Base */
        .kumar-cta-button {
            display: inline-block;
            font-family: 'Jost', sans-serif;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            text-decoration: none;
            transition: all 0.4s ease;
            white-space: nowrap;
            position: relative;
            overflow: hidden;
        }
        
        /* Button Styles */
        .kumar-btn-golden-shine::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: left 0.6s ease;
        }
        
        .kumar-btn-golden-shine:hover::before {
            left: 100%;
        }
        
        .kumar-btn-golden-shine:hover {
            transform: translateY(-2px);
        }
        
        .kumar-btn-elegant-border::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 100%;
            background: currentColor;
            transition: width 0.4s ease;
            z-index: -1;
        }
        
        .kumar-btn-elegant-border:hover::before {
            width: 100%;
        }
        
        .kumar-btn-diamond-cut {
            clip-path: polygon(10% 0%, 90% 0%, 100% 50%, 90% 100%, 10% 100%, 0% 50%);
            padding: 16px 50px !important;
        }
        
        .kumar-btn-royal-gradient:hover {
            transform: scale(1.05);
        }
        
        .kumar-btn-ornate-frame::before,
        .kumar-btn-ornate-frame::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 2px solid currentColor;
            transition: all 0.3s ease;
        }
        
        .kumar-btn-ornate-frame::before {
            top: 0;
            left: 0;
            border-right: none;
            border-bottom: none;
        }
        
        .kumar-btn-ornate-frame::after {
            bottom: 0;
            right: 0;
            border-left: none;
            border-top: none;
        }
        
        .kumar-btn-ornate-frame:hover::before,
        .kumar-btn-ornate-frame:hover::after {
            width: 100%;
            height: 100%;
        }
        
        .kumar-btn-glowing-edge:hover {
            box-shadow: 0 0 20px currentColor, inset 0 0 20px currentColor;
        }
        
        .kumar-btn-split-reveal {
            z-index: 1;
        }
        
        .kumar-btn-split-reveal::before,
        .kumar-btn-split-reveal::after {
            content: '';
            position: absolute;
            top: 0;
            width: 0;
            height: 100%;
            background: currentColor;
            transition: width 0.3s ease;
            z-index: -1;
        }
        
        .kumar-btn-split-reveal::before {
            left: 50%;
        }
        
        .kumar-btn-split-reveal::after {
            right: 50%;
        }
        
        .kumar-btn-split-reveal:hover::before,
        .kumar-btn-split-reveal:hover::after {
            width: 50%;
        }
        
        .kumar-btn-luxury-underline::before,
        .kumar-btn-luxury-underline::after {
            content: '◆';
            position: absolute;
            bottom: -12px;
            color: currentColor;
            font-size: 12px;
            opacity: 0;
            transition: all 0.3s ease;
        }
        
        .kumar-btn-luxury-underline::before {
            left: 0;
        }
        
        .kumar-btn-luxury-underline::after {
            right: 0;
        }
        
        .kumar-btn-luxury-underline:hover {
            transform: translateY(-2px);
        }
        
        .kumar-btn-luxury-underline:hover::before,
        .kumar-btn-luxury-underline:hover::after {
            opacity: 1;
        }
        
        .kumar-btn-premium-glow:hover {
            box-shadow: 0 0 40px currentColor, 0 0 60px currentColor, inset 0 0 20px rgba(255, 255, 255, 0.3);
            transform: scale(1.05);
        }
        
        .kumar-btn-embossed:hover {
            box-shadow: inset 5px 5px 15px rgba(0, 0, 0, 0.5),
                        inset -5px -5px 15px rgba(255, 255, 255, 0.02),
                        0 0 30px currentColor;
        }
        
        .kumar-btn-minimal-slide {
            position: relative;
        }
        
        .kumar-btn-minimal-slide::after {
            content: '→';
            position: absolute;
            right: 10px;
            opacity: 0;
            transition: all 0.3s ease;
        }
        
        .kumar-btn-minimal-slide:hover {
            padding-right: 45px !important;
        }
        
        .kumar-btn-minimal-slide:hover::after {
            opacity: 1;
            right: 15px;
        }
        
        .kumar-btn-heritage::before {
            content: '';
            position: absolute;
            inset: -3px;
            border: 1px solid currentColor;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .kumar-btn-heritage:hover::before {
            opacity: 1;
        }
        
        /* Mobile Menu Toggle */
        .kumar-menu-toggle {
            display: none;
            flex-direction: column;
            gap: 6px;
            cursor: pointer;
            padding: 10px;
            z-index: 10002;
        }
        
        .kumar-menu-toggle span {
            width: 28px;
            height: 2px;
            transition: all 0.3s ease;
            border-radius: 2px;
        }
        
        .kumar-menu-toggle.active span:nth-child(1) {
            transform: rotate(45deg) translate(8px, 8px);
        }
        
        .kumar-menu-toggle.active span:nth-child(2) {
            opacity: 0;
        }
        
        .kumar-menu-toggle.active span:nth-child(3) {
            transform: rotate(-45deg) translate(8px, -8px);
        }
        
        /* Mobile Backdrop */
        .kumar-mobile-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            z-index: 998;
            opacity: 0;
            visibility: hidden;
            transition: all 0.4s ease;
            backdrop-filter: blur(0px);
        }
        
        .kumar-mobile-backdrop.active {
            opacity: 1;
            visibility: visible;
            backdrop-filter: blur(8px);
        }
        
        /* Mobile Menu */
        .kumar-mobile-menu {
            position: fixed;
            top: 0;
            right: -100%;
            width: 100%;
            max-width: 400px;
            height: 100vh;
            padding: 100px 40px 40px;
            transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            box-shadow: -5px 0 30px rgba(0, 0, 0, 0.5);
            border-left: 1px solid rgba(212, 175, 55, 0.2);
            z-index: 1000;
            overflow-y: auto;
            backdrop-filter: blur(20px);
        }
        
        .kumar-mobile-menu.active {
            right: 0;
        }
        
        /* Mobile Menu Logo */
        .kumar-mobile-logo {
            text-align: center;
            padding: 0 0 30px 0;
            margin-bottom: 30px;
            border-bottom: 1px solid rgba(212, 175, 55, 0.2);
        }
        
        .kumar-mobile-logo .kumar-logo {
            font-size: 1.8rem;
            letter-spacing: 2px;
        }
        
        .kumar-mobile-logo .kumar-logo-image {
            max-width: 150px;
            height: auto;
            display: inline-block;
        }
        
        
        .kumar-mobile-nav-links {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin: 0;
            padding: 0;
            margin-bottom: 40px;
        }
        
        .kumar-mobile-nav-links a {
            font-family: 'Jost', sans-serif;
            font-size: 1.2rem;
            text-decoration: none;
            letter-spacing: 2px;
            transition: all 0.3s ease;
            display: block;
            padding: 10px 0;
        }
        
        .kumar-mobile-nav-links a:hover {
            padding-left: 10px;
        }
        
        .kumar-mobile-cta {
            margin-top: 40px;
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .kumar-header-container {
                padding: 20px 40px !important;
            }
        }
        
        @media (max-width: 768px) {
            .kumar-header-container {
                padding: 20px 30px !important;
            }
            
            .kumar-logo {
                font-size: 1.6rem !important;
            }
            
            .kumar-logo-image {
                max-width: 150px;
            }
            
            .kumar-tagline {
                font-size: 0.65rem !important;
            }
            
            .kumar-nav-links,
            .kumar-nav .kumar-cta-button {
                display: none;
            }
            
            .kumar-menu-toggle {
                display: flex;
            }
        }
        </style>
        
        <script>
        (function() {
            const header = document.getElementById('kumar-header-<?php echo esc_js($widget_id); ?>');
            const menuToggle = document.getElementById('kumar-menu-toggle-<?php echo esc_js($widget_id); ?>');
            const mobileMenu = document.getElementById('kumar-mobile-menu-<?php echo esc_js($widget_id); ?>');
            const backdrop = document.getElementById('kumar-backdrop-<?php echo esc_js($widget_id); ?>');
            const mobileLinks = mobileMenu.querySelectorAll('.kumar-mobile-nav-links a');
            
            // Header scroll effect
            window.addEventListener('scroll', function() {
                if (window.scrollY > 50) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }
            });
            
            // Mobile menu toggle
            menuToggle.addEventListener('click', function() {
                menuToggle.classList.toggle('active');
                mobileMenu.classList.toggle('active');
                backdrop.classList.toggle('active');
                document.body.style.overflow = mobileMenu.classList.contains('active') ? 'hidden' : '';
            });
            
            // Close on link click
            mobileLinks.forEach(function(link) {
                link.addEventListener('click', function() {
                    menuToggle.classList.remove('active');
                    mobileMenu.classList.remove('active');
                    backdrop.classList.remove('active');
                    document.body.style.overflow = '';
                });
            });
            
            // Close on backdrop click
            backdrop.addEventListener('click', function() {
                menuToggle.classList.remove('active');
                mobileMenu.classList.remove('active');
                backdrop.classList.remove('active');
                document.body.style.overflow = '';
            });
            
            // Close on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && mobileMenu.classList.contains('active')) {
                    menuToggle.classList.remove('active');
                    mobileMenu.classList.remove('active');
                    backdrop.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        })();
        </script>
        <?php
    }
}
?>