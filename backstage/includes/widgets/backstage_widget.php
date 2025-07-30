<?php
if (!defined('ABSPATH')) {
    exit;
}

class Elementor_Backstage_Widget extends \Elementor\Widget_Base {
    public function get_name() {
        return 'backstage_widget';
    }

    public function get_title() {
        return __('Backstage Image', 'backstage-by-mq');
    }

    public function get_icon() {
        return 'eicon-image-box';
    }

    public function get_categories() {
        return ['basic'];
    }

    protected function register_controls() {
        /**************************************************************************************************/
        /*******************************************Container Styling**************************************/
        /**************************************************************************************************/
        $this->start_controls_section(
            'container_section',
            [
                'label' => __('Container', 'backstage-by-mq'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'width',
            [
                'label' => esc_html__('Width', 'backstage-by-mq'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['%', 'px', 'vw'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1200,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'vw' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 100,
                ],
                'responsive' => [
                    'desktop' => [
                        'unit' => '%',
                        'size' => 100,
                    ],
                    'tablet' => [
                        'unit' => '%',
                        'size' => 90,
                    ],
                    'mobile' => [
                        'unit' => '%',
                        'size' => 95,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .backstage-container' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'height',
            [
                'label' => esc_html__('Height', 'backstage-by-mq'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'vh'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1200,
                    ],
                    'vh' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 200,
                ],
                'responsive' => [
                    'desktop' => [
                        'unit' => 'px',
                        'size' => 200,
                    ],
                    'tablet' => [
                        'unit' => 'px',
                        'size' => 180,
                    ],
                    'mobile' => [
                        'unit' => 'px',
                        'size' => 150,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .backstage-container' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'container_alignment',
            [
                'label' => esc_html__('Alignment', 'backstage-by-mq'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'backstage-by-mq'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'backstage-by-mq'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'backstage-by-mq'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .backstage-container' => 'margin: {{VALUE}};',
                ],
                'selectors_dictionary' => [
                    'left' => '0 auto 0 0',
                    'center' => '0 auto',
                    'right' => '0 0 0 auto',
                ],
            ]
        );

        $this->add_responsive_control(
            'container_radius',
            [
                'label' => __('Container Border Radius', 'backstage-by-mq'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'default' => [
                    'top' => 0,
                    'right' => 0,
                    'bottom' => 0,
                    'left' => 0,
                    'unit' => 'px',
                ],
                'responsive' => [
                    'desktop' => [
                        'top' => 0,
                        'right' => 0,
                        'bottom' => 0,
                        'left' => 0,
                        'unit' => 'px',
                    ],
                    'tablet' => [
                        'top' => 0,
                        'right' => 0,
                        'bottom' => 0,
                        'left' => 0,
                        'unit' => 'px',
                    ],
                    'mobile' => [
                        'top' => 0,
                        'right' => 0,
                        'bottom' => 0,
                        'left' => 0,
                        'unit' => 'px',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .backstage-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                'label' => __('Site Logo', 'backstage-by-mq'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'logo_position_popover',
            [
                'label' => esc_html__('Logo Position', 'backstage-by-mq'),
                'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
                'label_off' => esc_html__('Default', 'backstage-by-mq'),
                'label_on' => esc_html__('Custom', 'backstage-by-mq'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->start_popover();

        $this->add_control(
            'logo_vertical_position',
            [
                'label' => esc_html__('Vertical Position', 'backstage-by-mq'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'top' => esc_html__('Top', 'backstage-by-mq'),
                    'bottom' => esc_html__('Bottom', 'backstage-by-mq'),
                ],
                'default' => 'top',
                'frontend_available' => true,
            ]
        );

        $this->add_responsive_control(
            'logo_vertical_value',
            [
                'label' => esc_html__('Vertical Value', 'backstage-by-mq'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'vh'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 200,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'vh' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 20,
                ],
                'responsive' => [
                    'desktop' => [
                        'unit' => 'px',
                        'size' => 20,
                    ],
                    'tablet' => [
                        'unit' => 'px',
                        'size' => 15,
                    ],
                    'mobile' => [
                        'unit' => 'px',
                        'size' => 10,
                    ],
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
                'label' => esc_html__('Horizontal Position', 'backstage-by-mq'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'left' => esc_html__('Left', 'backstage-by-mq'),
                    'right' => esc_html__('Right', 'backstage-by-mq'),
                ],
                'default' => 'left',
                'frontend_available' => true,
            ]
        );

        $this->add_responsive_control(
            'logo_horizontal_value',
            [
                'label' => esc_html__('Horizontal Value', 'backstage-by-mq'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'vw'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 200,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'vw' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 20,
                ],
                'responsive' => [
                    'desktop' => [
                        'unit' => 'px',
                        'size' => 20,
                    ],
                    'tablet' => [
                        'unit' => 'px',
                        'size' => 15,
                    ],
                    'mobile' => [
                        'unit' => 'px',
                        'size' => 10,
                    ],
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

        $this->add_responsive_control(
            'logo_height',
            [
                'label' => esc_html__('Height', 'backstage-by-mq'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'vh'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 200,
                    ],
                    'vh' => [
                        'min' => 1,
                        'max' => 20,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 40,
                ],
                'responsive' => [
                    'desktop' => [
                        'unit' => 'px',
                        'size' => 40,
                    ],
                    'tablet' => [
                        'unit' => 'px',
                        'size' => 35,
                    ],
                    'mobile' => [
                        'unit' => 'px',
                        'size' => 30,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .backstage-logo' => 'height: {{SIZE}}{{UNIT}}; width: auto;',
                ],
            ]
        );

        $this->add_responsive_control(
            'logo_opacity',
            [
                'label' => esc_html__('Opacity', 'backstage-by-mq'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 0,
                'max' => 1,
                'step' => 0.1,
                'default' => 0.8,
                'responsive' => [
                    'desktop' => [
                        'value' => 0.8,
                    ],
                    'tablet' => [
                        'value' => 0.8,
                    ],
                    'mobile' => [
                        'value' => 0.8,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .backstage-logo' => 'opacity: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'logo_transition_duration',
            [
                'label' => esc_html__('Transition Duration (seconds)', 'backstage-by-mq'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 0,
                'max' => 2,
                'step' => 0.1,
                'default' => 0.3,
                'responsive' => [
                    'desktop' => [
                        'value' => 0.3,
                    ],
                    'tablet' => [
                        'value' => 0.3,
                    ],
                    'mobile' => [
                        'value' => 0.3,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .backstage-logo' => 'transition: opacity {{VALUE}}s ease-in-out;',
                ],
            ]
        );

        $this->add_responsive_control(
            'logo_hover_opacity',
            [
                'label' => esc_html__('Hover Opacity', 'backstage-by-mq'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 0,
                'max' => 1,
                'step' => 0.1,
                'default' => 1,
                'responsive' => [
                    'desktop' => [
                        'value' => 1,
                    ],
                    'tablet' => [
                        'value' => 1,
                    ],
                    'mobile' => [
                        'value' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .backstage-logo:hover' => 'opacity: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        /**************************************************************************************************/
        /*******************************************Image Styling****************************************/
        /**************************************************************************************************/
        $this->start_controls_section(
            'image_style_section',
            [
                'label' => esc_html__('Image', 'backstage-by-mq'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'image_position_popover',
            [
                'label' => esc_html__('Image Position', 'backstage-by-mq'),
                'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
                'label_off' => esc_html__('Default', 'backstage-by-mq'),
                'label_on' => esc_html__('Custom', 'backstage-by-mq'),
                'return_value' => 'yes',
                'default' => '',
            ]
        );

        $this->start_popover();

        $this->add_control(
            'image_vertical_position',
            [
                'label' => esc_html__('Vertical Position', 'backstage-by-mq'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'top' => esc_html__('Top', 'backstage-by-mq'),
                    'bottom' => esc_html__('Bottom', 'backstage-by-mq'),
                ],
                'default' => 'top',
                'frontend_available' => true,
            ]
        );

        $this->add_responsive_control(
            'image_vertical_value',
            [
                'label' => esc_html__('Vertical Value', 'backstage-by-mq'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'vh'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'vh' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 50,
                ],
                'responsive' => [
                    'desktop' => [
                        'unit' => '%',
                        'size' => 50,
                    ],
                    'tablet' => [
                        'unit' => '%',
                        'size' => 50,
                    ],
                    'mobile' => [
                        'unit' => '%',
                        'size' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .backstage-image' => 'position: absolute; {{image_vertical_position.VALUE}}: {{SIZE}}{{UNIT}}; transform: none;',
                ],
                'condition' => [
                    'image_position_popover' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'image_horizontal_position',
            [
                'label' => esc_html__('Horizontal Position', 'backstage-by-mq'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'left' => esc_html__('Left', 'backstage-by-mq'),
                    'right' => esc_html__('Right', 'backstage-by-mq'),
                ],
                'default' => 'left',
                'frontend_available' => true,
            ]
        );

        $this->add_responsive_control(
            'image_horizontal_value',
            [
                'label' => esc_html__('Horizontal Value', 'backstage-by-mq'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'vw'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'vw' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 50,
                ],
                'responsive' => [
                    'desktop' => [
                        'unit' => '%',
                        'size' => 50,
                    ],
                    'tablet' => [
                        'unit' => '%',
                        'size' => 50,
                    ],
                    'mobile' => [
                        'unit' => '%',
                        'size' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .backstage-image' => 'position: absolute; {{image_horizontal_position.VALUE}}: {{SIZE}}{{UNIT}}; transform: none;',
                ],
                'condition' => [
                    'image_position_popover' => 'yes',
                ],
            ]
        );

        $this->end_popover();

        $this->add_responsive_control(
            'image_width',
            [
                'label' => esc_html__('Image Width', 'backstage-by-mq'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'vw'],
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 500,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                    'vw' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 200,
                ],
                'responsive' => [
                    'desktop' => [
                        'unit' => 'px',
                        'size' => 200,
                    ],
                    'tablet' => [
                        'unit' => 'px',
                        'size' => 180,
                    ],
                    'mobile' => [
                        'unit' => 'px',
                        'size' => 150,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .backstage-image' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_height',
            [
                'label' => esc_html__('Image Height', 'backstage-by-mq'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'vh'],
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 500,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                    'vh' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 200,
                ],
                'responsive' => [
                    'desktop' => [
                        'unit' => 'px',
                        'size' => 200,
                    ],
                    'tablet' => [
                        'unit' => 'px',
                        'size' => 180,
                    ],
                    'mobile' => [
                        'unit' => 'px',
                        'size' => 150,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .backstage-image' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'image_object_fit',
            [
                'label' => esc_html__('Object Fit', 'backstage-by-mq'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'cover' => esc_html__('Cover', 'backstage-by-mq'),
                    'contain' => esc_html__('Contain', 'backstage-by-mq'),
                    'fill' => esc_html__('Fill', 'backstage-by-mq'),
                    'none' => esc_html__('None', 'backstage-by-mq'),
                    'scale-down' => esc_html__('Scale Down', 'backstage-by-mq'),
                ],
                'default' => 'cover',
                'selectors' => [
                    '{{WRAPPER}} .backstage-image' => 'object-fit: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_radius',
            [
                'label' => __('Image Border Radius', 'backstage-by-mq'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'default' => [
                    'top' => 0,
                    'right' => 0,
                    'bottom' => 0,
                    'left' => 0,
                    'unit' => 'px',
                ],
                'responsive' => [
                    'desktop' => [
                        'top' => 0,
                        'right' => 0,
                        'bottom' => 0,
                        'left' => 0,
                        'unit' => 'px',
                    ],
                    'tablet' => [
                        'top' => 0,
                        'right' => 0,
                        'bottom' => 0,
                        'left' => 0,
                        'unit' => 'px',
                    ],
                    'mobile' => [
                        'top' => 0,
                        'right' => 0,
                        'bottom' => 0,
                        'left' => 0,
                        'unit' => 'px',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .backstage-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'selector' => '{{WRAPPER}} .backstage-image',
			]
		);

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'image_shadow',
                'label' => esc_html__('Image Shadow', 'backstage-by-mq'),
                'selector' => '{{WRAPPER}} .backstage-image',
                'fields_options' => [
                    'box_shadow' => [
                        'default' => [
                            'horizontal' => 2,
                            'vertical' => 2,
                            'blur' => 4,
                            'spread' => 0,
                            'color' => 'rgba(0, 0, 0, 0.6)',
                        ],
                    ],
                ],
            ]
        );

        $this->end_controls_section();

        /**************************************************************************************************/
        /*******************************************Content Section****************************************/
        /**************************************************************************************************/
        $this->start_controls_section(
            'content_style_section',
            [
                'label' => esc_html__('Content Styling', 'backstage-by-mq'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'content_position_popover',
            [
                'label' => esc_html__('Content Position', 'backstage-by-mq'),
                'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
                'label_off' => esc_html__('Default', 'backstage-by-mq'),
                'label_on' => esc_html__('Custom', 'backstage-by-mq'),
                'return_value' => 'yes',
                'default' => '',
            ]
        );

        $this->start_popover();

        $this->add_control(
            'content_vertical_position',
            [
                'label' => esc_html__('Vertical Position', 'backstage-by-mq'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'top' => esc_html__('Top', 'backstage-by-mq'),
                    'bottom' => esc_html__('Bottom', 'backstage-by-mq'),
                ],
                'default' => 'top',
                'frontend_available' => true,
            ]
        );

        $this->add_responsive_control(
            'content_vertical_value',
            [
                'label' => esc_html__('Vertical Value', 'backstage-by-mq'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'vh'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'vh' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 50,
                ],
                'responsive' => [
                    'desktop' => [
                        'unit' => '%',
                        'size' => 50,
                    ],
                    'tablet' => [
                        'unit' => '%',
                        'size' => 50,
                    ],
                    'mobile' => [
                        'unit' => '%',
                        'size' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .backstage-title' => 'position: absolute; {{content_vertical_position.VALUE}}: {{SIZE}}{{UNIT}}; transform: none;',
                ],
                'condition' => [
                    'content_position_popover' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'content_horizontal_position',
            [
                'label' => esc_html__('Horizontal Position', 'backstage-by-mq'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'left' => esc_html__('Left', 'backstage-by-mq'),
                    'right' => esc_html__('Right', 'backstage-by-mq'),
                ],
                'default' => 'left',
                'frontend_available' => true,
            ]
        );

        $this->add_responsive_control(
            'content_horizontal_value',
            [
                'label' => esc_html__('Horizontal Value', 'backstage-by-mq'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'vw'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'vw' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 50,
                ],
                'responsive' => [
                    'desktop' => [
                        'unit' => '%',
                        'size' => 50,
                    ],
                    'tablet' => [
                        'unit' => '%',
                        'size' => 50,
                    ],
                    'mobile' => [
                        'unit' => '%',
                        'size' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .backstage-title' => 'position: absolute; {{content_horizontal_position.VALUE}}: {{SIZE}}{{UNIT}}; transform: none;',
                ],
                'condition' => [
                    'content_position_popover' => 'yes',
                ],
            ]
        );

        $this->end_popover();

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'content_typography',
                'label' => esc_html__('Typography', 'backstage-by-mq'),
                'selector' => '{{WRAPPER}} .backstage-title',
                'responsive' => true,
                'fields_options' => [
                    'font_size' => [
                        'default' => [
                            'unit' => 'px',
                            'size' => 50,
                        ],
                        'responsive' => [
                            'desktop' => [
                                'unit' => 'px',
                                'size' => 50,
                            ],
                            'tablet' => [
                                'unit' => 'px',
                                'size' => 40,
                            ],
                            'mobile' => [
                                'unit' => 'px',
                                'size' => 30,
                            ],
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
                        'responsive' => [
                            'desktop' => [
                                'unit' => 'px',
                                'size' => 1,
                            ],
                            'tablet' => [
                                'unit' => 'px',
                                'size' => 0.8,
                            ],
                            'mobile' => [
                                'unit' => 'px',
                                'size' => 0.6,
                            ],
                        ],
                    ],
                ],
            ]
        );

        $this->add_responsive_control(
            'content_text_color',
            [
                'label' => esc_html__('Text Color', 'backstage-by-mq'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'responsive' => [
                    'desktop' => [
                        'value' => '#FFFFFF',
                    ],
                    'tablet' => [
                        'value' => '#FFFFFF',
                    ],
                    'mobile' => [
                        'value' => '#FFFFFF',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .backstage-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'content_text_shadow',
                'label' => esc_html__('Text Shadow', 'backstage-by-mq'),
                'selector' => '{{WRAPPER}} .backstage-title',
                'fields_options' => [
                    'text_shadow' => [
                        'default' => [
                            'horizontal' => 2,
                            'vertical' => 2,
                            'blur' => 4,
                            'color' => 'rgba(0, 0, 0, 0.6)',
                        ],
                        'responsive' => [
                            'desktop' => [
                                'horizontal' => 2,
                                'vertical' => 2,
                                'blur' => 4,
                                'color' => 'rgba(0, 0, 0, 0.6)',
                            ],
                            'tablet' => [
                                'horizontal' => 1.5,
                                'vertical' => 1.5,
                                'blur' => 3,
                                'color' => 'rgba(0, 0, 0, 0.6)',
                            ],
                            'mobile' => [
                                'horizontal' => 1,
                                'vertical' => 1,
                                'blur' => 2,
                                'color' => 'rgba(0, 0, 0, 0.6)',
                            ],
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

        $content_style = '';
        if (empty($settings['content_position_popover'])) {
            $content_style = 'style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);"';
        }

        ?>
        <style>
            .backstage-container {
                position: relative;
                background-size: cover;
                background-position: center;
                overflow: hidden;
            }
            .backstage-image, .backstage-title, .backstage-logo {
                transition: all 0.3s ease-in-out;
            }
            @media (max-width: 1024px) {
                .backstage-container {
                    background-size: contain;
                }
                .backstage-image {
                    max-width: 90%;
                }
                .backstage-title {
                    font-size: 40px;
                }
            }
            @media (max-width: 767px) {
                .backstage-container {
                    background-size: contain;
                }
                .backstage-image {
                    max-width: 85%;
                }
                .backstage-title {
                    font-size: 30px;
                }
                .backstage-logo {
                    max-height: 30px;
                }
            }
        </style>
        <div class="backstage-container" style="background-image: url(<?php echo esc_url($background_url); ?>);">
            <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($title); ?>" class="backstage-image" <?php echo $content_style; ?>>
            <p class="backstage-title" <?php echo $content_style; ?>><?php echo esc_html($title); ?></p>
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