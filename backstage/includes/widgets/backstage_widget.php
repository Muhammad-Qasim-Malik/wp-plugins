<?php
if (!defined('ABSPATH')) {
    exit;
}

class Elementor_Backstage_Widget extends \Elementor\Widget_Base {
    public function get_name() {
        return 'backstage_widget';
    }

    public function get_title() {
        return __('Backstage Image', 'backstage');
    }

    public function get_icon() {
        return 'eicon-image-box';
    }

    public function get_categories() {
        return ['basic'];
    }

    protected function _register_controls() {
        /**************************************************************************************************/
        /*******************************************Container Styling**************************************/
        /**************************************************************************************************/
        $this->start_controls_section(
            'container_section',
            [
                'label' => __('Container', 'backstage'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'width',
            [
                'label' => esc_html__('Width', 'backstage'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['%', 'px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1200,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 100,
                ],
                'selectors' => [
                    '{{WRAPPER}} .backstage-container' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'height',
            [
                'label' => esc_html__('Height', 'backstage'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1200,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 200,
                ],
                'selectors' => [
                    '{{WRAPPER}} .backstage-container' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'container_alignment',
            [
                'label' => esc_html__('Alignment', 'backstage'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'backstage'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'backstage'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'backstage'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .backstage-container' => 'display: block; margin: {{VALUE}};',
                ],
                'selectors_dictionary' => [
                    'left' => '0 auto 0 0',
                    'center' => '0 auto',
                    'right' => '0 0 0 auto',
                ],
            ]
        );

        $this->end_controls_section();

        /**************************************************************************************************/
        /*******************************************Logo Styling*******************************************/
        /**************************************************************************************************/
        $this->start_controls_section(
            'logo_section',
            [
                'label' => __('Site Logo', 'backstage'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'logo_position_popover',
            [
                'label' => esc_html__('Logo Position', 'backstage'),
                'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
                'label_off' => esc_html__('Default', 'backstage'),
                'label_on' => esc_html__('Custom', 'backstage'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->start_popover();

        $this->add_control(
            'logo_vertical_position',
            [
                'label' => esc_html__('Vertical Position', 'backstage'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'top' => esc_html__('Top', 'backstage'),
                    'bottom' => esc_html__('Bottom', 'backstage'),
                ],
                'default' => 'top',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'logo_vertical_value',
            [
                'label' => esc_html__('Vertical Value', 'backstage'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 200,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .backstage-logo' => 'position: absolute; {{logo_vertical_position.VALUE}}: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'logo_position_popover' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'logo_horizontal_position',
            [
                'label' => esc_html__('Horizontal Position', 'backstage'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'left' => esc_html__('Left', 'backstage'),
                    'right' => esc_html__('Right', 'backstage'),
                ],
                'default' => 'left',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'logo_horizontal_value',
            [
                'label' => esc_html__('Horizontal Value', 'backstage'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 200,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .backstage-logo' => 'position: absolute; {{logo_horizontal_position.VALUE}}: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'logo_position_popover' => 'yes',
                ],
            ]
        );

        $this->end_popover();

        $this->add_control(
            'logo_height',
            [
                'label' => esc_html__('Height', 'backstage'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 200,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 40,
                ],
                'selectors' => [
                    '{{WRAPPER}} .backstage-logo' => 'height: {{SIZE}}{{UNIT}}; width: auto;',
                ],
            ]
        );

        $this->add_control(
            'logo_opacity',
            [
                'label' => esc_html__('Opacity', 'backstage'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 0,
                'max' => 1,
                'step' => 0.1,
                'default' => 0.8,
                'selectors' => [
                    '{{WRAPPER}} .backstage-logo' => 'opacity: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'logo_transition_duration',
            [
                'label' => esc_html__('Transition Duration (seconds)', 'backstage'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 0,
                'max' => 2,
                'step' => 0.1,
                'default' => 0.3,
                'selectors' => [
                    '{{WRAPPER}} .backstage-logo' => 'transition: opacity {{VALUE}}s ease-in-out;',
                ],
            ]
        );

        $this->add_control(
            'logo_hover_opacity',
            [
                'label' => esc_html__('Hover Opacity', 'backstage'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 0,
                'max' => 1,
                'step' => 0.1,
                'default' => 1,
                'selectors' => [
                    '{{WRAPPER}} .backstage-logo:hover' => 'opacity: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        /**************************************************************************************************/
        /*******************************************Content Styling****************************************/
        /**************************************************************************************************/
        $this->start_controls_section(
            'content_style_section',
            [
                'label' => esc_html__('Content Styling', 'backstage'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'content_position_popover',
            [
                'label' => esc_html__('Content Position', 'backstage'),
                'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
                'label_off' => esc_html__('Default', 'backstage'),
                'label_on' => esc_html__('Custom', 'backstage'),
                'return_value' => 'yes',
                'default' => '',
            ]
        );

        $this->start_popover();

        $this->add_control(
            'content_vertical_position',
            [
                'label' => esc_html__('Vertical Position', 'backstage'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'top' => esc_html__('Top', 'backstage'),
                    'bottom' => esc_html__('Bottom', 'backstage'),
                ],
                'default' => 'top',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'content_vertical_value',
            [
                'label' => esc_html__('Vertical Value', 'backstage'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 50,
                ],
                'selectors' => [
                    '{{WRAPPER}} .content' => 'position: absolute; {{content_vertical_position.VALUE}}: {{SIZE}}{{UNIT}}; transform: none;',
                ],
                'condition' => [
                    'content_position_popover' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'content_horizontal_position',
            [
                'label' => esc_html__('Horizontal Position', 'backstage'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'left' => esc_html__('Left', 'backstage'),
                    'right' => esc_html__('Right', 'backstage'),
                ],
                'default' => 'left',
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'content_horizontal_value',
            [
                'label' => esc_html__('Horizontal Value', 'backstage'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 50,
                ],
                'selectors' => [
                    '{{WRAPPER}} .content' => 'position: absolute; {{content_horizontal_position.VALUE}}: {{SIZE}}{{UNIT}}; transform: none;',
                ],
                'condition' => [
                    'content_position_popover' => 'yes',
                ],
            ]
        );

        $this->end_popover();

        $this->add_control(
            'image_width',
            [
                'label' => esc_html__('Image Width', 'backstage'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 200,
                ],
                'selectors' => [
                    '{{WRAPPER}} .content.backstage-image' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'image_height',
            [
                'label' => esc_html__('Image Height', 'backstage'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 200,
                ],
                'selectors' => [
                    '{{WRAPPER}} .content.backstage-image' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'image_object_fit',
            [
                'label' => esc_html__('Object Fit', 'backstage'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'cover' => esc_html__('Cover', 'backstage'),
                    'contain' => esc_html__('Contain', 'backstage'),
                    'fill' => esc_html__('Fill', 'backstage'),
                    'none' => esc_html__('None', 'backstage'),
                    'scale-down' => esc_html__('Scale Down', 'backstage'),
                ],
                'default' => 'cover',
                'selectors' => [
                    '{{WRAPPER}} .content.backstage-image' => 'object-fit: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'content_typography',
                'label' => esc_html__('Typography', 'backstage'),
                'selector' => '{{WRAPPER}} .content.backstage-title',
                'fields_options' => [
                    'font_size' => [
                        'default' => [
                            'unit' => 'px',
                            'size' => 50,
                        ],
                    ],
                    'font_weight' => [
                        'default' => 'bold',
                    ],
                    'letter_spacing' => [
                        'default' => [
                            'unit' => 'px',
                            'size' => 1,
                        ],
                    ],
                ],
            ]
        );

        $this->add_control(
            'content_text_color',
            [
                'label' => esc_html__('Text Color', 'backstage'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .content.backstage-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'content_text_shadow',
                'label' => esc_html__('Text Shadow', 'backstage'),
                'selector' => '{{WRAPPER}} .content.backstage-title',
                'fields_options' => [
                    'text_shadow' => [
                        'default' => [
                            'horizontal' => 2,
                            'vertical' => 2,
                            'blur' => 4,
                            'color' => 'rgba(0, 0, 0, 0.6)',
                        ],
                    ],
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $title = get_the_title();
        $background_image = get_option('backstage_bg_image', '');
        $background_url = esc_url($background_image);
        $site_logo = wp_get_attachment_image_url(get_theme_mod('custom_logo'), 'full');
        $image_url = get_post_meta(get_the_ID(), '_image_url', true);

        // Apply default centering for .content if position popover is not enabled
        $content_style = '';
        if (empty($settings['content_position_popover'])) {
            $content_style = 'style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);"';
        }

        ?>
        <div class="backstage-container" style="background-image: url(<?php echo esc_url($background_url); ?>);">
            <?php if ($image_url) : ?>
                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($title); ?>" class="content backstage-image" <?php echo $content_style; ?>>
            <?php else : ?>
                <p class="content backstage-title" <?php echo $content_style; ?>><?php echo esc_html($title); ?></p>
            <?php endif; ?>
            <?php if ($site_logo) : ?>
                <img src="<?php echo esc_url($site_logo); ?>" alt="Logo" class="backstage-logo">
            <?php endif; ?>
        </div>
        <?php
    }
}

function register_backstage_elementor_widget($widgets_manager) {
    $widgets_manager->register(new \Elementor_Backstage_Widget());
}

add_action('elementor/widgets/register', 'register_backstage_elementor_widget');
?>