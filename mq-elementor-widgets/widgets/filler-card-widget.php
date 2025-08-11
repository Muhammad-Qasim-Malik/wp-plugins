<?php
if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

class MQ_Filler_Card_Widget extends Widget_Base {

    public function get_name() {
        return 'mq_filler_card';
    }

    public function get_title() {
        return __( 'MQ Filler Card', 'mq-elementor-widgets' );
    }

    public function get_icon() {
        return 'eicon-kit-details';
    }

    public function get_categories() {
        return [ 'basic' ];
    }

    protected function _register_controls() {

        /**************************************************************************************************/
        /*******************************************Content Section****************************************/
        /**************************************************************************************************/
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Content', 'mq-elementor-widgets' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'position_selector',
            [
                'label'   => __( 'Position', 'mq-elementor-widgets' ),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    'top-left'     => __( 'Fill From Top Left', 'mq-elementor-widgets' ),
                    'top-right'    => __( 'Fill From Top Right', 'mq-elementor-widgets' ),
                    'bottom-left'  => __( 'Fill From Bottom Left', 'mq-elementor-widgets' ),
                    'bottom-right' => __( 'Fill From Bottom Right', 'mq-elementor-widgets' ),
                ],
                'default' => 'top-left',
            ]
        );

        // Icon
        $this->add_control(
            'icon',
            [
                'label'     => esc_html__( 'Icon', 'mq-elementor-widgets' ),
                'type'      => Controls_Manager::ICONS,
                'default'   => [
                    'value' => 'fas fa-circle', 
                    'library' => 'fa-solid',   
                ]
            ]
        );

        $this->add_control(
            'icon_position',
            [
                'label'   => __( 'Icon Position', 'mq-elementor-widgets' ),
                'type'    => Controls_Manager::CHOOSE,
                'options' => [
                    'top'    => [
                        'title' => __( 'Top', 'mq-elementor-widgets' ),
                        'icon'  => 'eicon-v-align-top',
                    ],
                    'bottom' => [
                        'title' => __( 'Bottom', 'mq-elementor-widgets' ),
                        'icon'  => 'eicon-v-align-bottom',
                    ],
                    'left'   => [
                        'title' => __( 'Left', 'mq-elementor-widgets' ),
                        'icon'  => 'eicon-h-align-left',
                    ],
                    'right'  => [
                        'title' => __( 'Right', 'mq-elementor-widgets' ),
                        'icon'  => 'eicon-h-align-right',
                    ],
                ],
                'default' => 'top',
                'toggle'  => false,
            ]
        );

        // Card
        $this->add_control(
            'card_alignment',
            [
                'label'   => __( 'Card Alignment', 'mq-elementor-widgets' ),
                'type'    => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start'   => [
                        'title' => __( 'Left', 'mq-elementor-widgets' ),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'mq-elementor-widgets' ),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'flex-end'  => [
                        'title' => __( 'Right', 'mq-elementor-widgets' ),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'center',
                'toggle'  => false,
            ]
        );

         // Text Alignment control for title and description
        $this->add_control(
            'text_alignment',
            [
                'label'   => __( 'Text Alignment', 'mq-elementor-widgets' ),
                'type'    => Controls_Manager::CHOOSE,
                'options' => [
                    'left'   => [
                        'title' => __( 'Left', 'mq-elementor-widgets' ),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'mq-elementor-widgets' ),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right'  => [
                        'title' => __( 'Right', 'mq-elementor-widgets' ),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'center',
                'toggle'  => false,
            ]
        );

        // Title Control
        $this->add_control(
            'title',
            [
                'label'   => __( 'Title', 'mq-elementor-widgets' ),
                'type'    => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => __( 'Type your title here.', 'mq-elementor-widgets' ),
            ]
        );

        $this->add_control(
            'description',
            [
                'label'   => __( 'Description', 'mq-elementor-widgets' ),
                'type'    => Controls_Manager::TEXTAREA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => __( 'Type your Description here.', 'mq-elementor-widgets' ),
            ]
        );

        // Button Link
        $this->add_control(
            'button_link',
            [
                'label'     => __( 'Link', 'mq-elementor-widgets' ),
                'type'      => Controls_Manager::URL,
                'placeholder' => __( 'https://your-link.com', 'mq-elementor-widgets' ),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->end_controls_section();

        /**************************************************************************************************/
        /*********************************************Style Section****************************************/
        /**************************************************************************************************/
        $this->start_controls_section(
            'style_section',
            [
                'label' => __( 'Style', 'mq-elementor-widgets' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'background',
                'label'     => __( 'Background', 'mq-elementor-widgets' ),
                'types'     => [ 'classic', 'gradient', 'image' ],
                'selector'  => '{{WRAPPER}} .mq-filler-card',
            ]
        );

        // Icon Size Control
        $this->add_control(
            'icon_size',
            [
                'label'   => __( 'Icon Size', 'mq-elementor-widgets' ),
                'type'    => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em' ],
                'range'   => [
                    'px' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .mq-filler-card .mq-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
                'default'   => [
                    'unit' => 'px',
                    'size' => 30,
                ],
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label'     => __( 'Icon Color', 'mq-elementor-widgets' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#000000', 
                'selectors' => [
                    '{{WRAPPER}} .mq-filler-card .mq-icon' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_gap',
            [
                'label'     => __( 'Icon Gap', 'mq-elementor-widgets' ),
                'type'      => Controls_Manager::SLIDER,
                'size_units'=> ['px', 'em', '%'],
                'default'   => [
                    'unit' => 'px',
                    'size' => 10
                ],
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .mq-filler-card' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'      => 'title_typography',
                'label'     => __( 'Title Typography', 'mq-elementor-widgets' ),
                'selector'  => '{{WRAPPER}} .mq-filler-card .title',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label'     => __( 'Title Color', 'mq-elementor-widgets' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#000000', 
                'selectors' => [
                    '{{WRAPPER}} .mq-filler-card .title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'      => 'description_typography',
                'label'     => __( 'Title Typography', 'mq-elementor-widgets' ),
                'selector'  => '{{WRAPPER}} .mq-filler-card .description',
            ]
        );

        $this->add_control(
            'description_color',
            [
                'label'     => __( 'Description Color', 'mq-elementor-widgets' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#000000', 
                'selectors' => [
                    '{{WRAPPER}} .mq-filler-card .description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'card_padding',
            [
                'label'      => __( 'Padding', 'mq-elementor-widgets' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .mq-filler-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'card_radius',
            [
                'label'      => __( 'Border Radius', 'mq-elementor-widgets' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .mq-filler-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .mq-filler-card::before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ] 
        );

        $this->end_controls_section();

        /**************************************************************************************************/
        /*********************************************Hover Section****************************************/
        /**************************************************************************************************/
        $this->start_controls_section(
            'hover_section',
            [
                'label' => __( 'Hover', 'mq-elementor-widgets' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'filler_background',
                'label'     => __( 'Background', 'mq-elementor-widgets' ),
                'types'     => [ 'classic', 'gradient', 'image' ],
                'selector'  => '{{WRAPPER}} .mq-filler-card::before',
            ]
        );
        $this->add_control(
            'transition_duration',
            [
                'label' => __( 'Transition Duration', 'mq-elementor-widgets' ),
                'type'  => Controls_Manager::SLIDER,
                'size_units' => ['s', 'ms'],
                'range' => [
                    's' => [
                        'min' => 0.1,
                        'max' => 5,
                        'step' => 0.1,
                    ],
                    'ms' => [
                        'min' => 100,
                        'max' => 5000,
                        'step' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .mq-filler-card::before' => 'transition: all {{SIZE}}{{UNIT}} linear;',
                ],
                'default' => [
                    'unit' => 's',
                    'size' => 0.3,
                ],
            ]
        );


        $this->add_control(
            'icon_hover_color',
            [
                'label'     => __( 'Icon Color', 'mq-elementor-widgets' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#000000', 
                'selectors' => [
                    '{{WRAPPER}} .mq-filler-card:hover .mq-icon' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_hover_color',
            [
                'label'     => __( 'Title Color', 'mq-elementor-widgets' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#000000', 
                'selectors' => [
                    '{{WRAPPER}} .mq-filler-card:hover .title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'description_hover_color',
            [
                'label'     => __( 'Description Color', 'mq-elementor-widgets' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#000000', 
                'selectors' => [
                    '{{WRAPPER}} .mq-filler-card:hover .description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render(){
        $settings = $this->get_settings_for_display();  

        $card_alignment = isset($settings['card_alignment']) ? $settings['card_alignment'] : 'center';
        $text_alignment = isset($settings['text_alignment']) ? $settings['text_alignment'] : 'center';
        $position = isset($settings['position_selector']) ? $settings['position_selector'] : 'top-left';

        $icon_position = isset($settings['icon_position']) ? $settings['icon_position'] : 'top';
        $flex_direction = ($icon_position == 'left' || $icon_position == 'right') ? 'row' : 'column';
        ?>
        <a href="<?php echo $settings['button_link']['url'] ?>" >
            <div class="mq-filler-card" data-position="<?php echo esc_attr($position); ?>"  style="flex-direction: <?php echo $flex_direction; ?>; align-items: <?php echo $card_alignment; ?>;">
                <?php 
                    $icon = !empty($settings['icon']) ? '<i class="' . esc_attr($settings['icon']['library']) . ' ' . esc_attr($settings['icon']['value']) . ' mq-icon"></i>' : '';

                    $content = '<div class="filler-card-text">';
                    $content .= !empty($settings['title']) ? '<h2 class="title" style="text-align: '. $text_alignment .'">' . esc_html($settings['title']) . '</h2>' : '';
                    $content .= !empty($settings['description']) ? '<p class="description" style="text-align: '. $text_alignment .'">' . esc_html($settings['description']) . '</p>' : '';
                    $content .= '</div>';

                    if ($icon_position == 'top') {
                        echo $icon . $content;
                    } elseif ($icon_position == 'left') {
                        echo $icon . $content;
                    } elseif ($icon_position == 'right') {
                        echo $content . $icon;
                    } else {
                        echo $content . $icon;
                    }
                ?>
            </div>
        </a>
        <?php 
    }
}

function register_mq_filler_card_widget( $widgets_manager ) {
    require_once( __DIR__ . '/filler-card-widget.php' );
    $widgets_manager->register( new \MQ_Filler_Card_Widget() );
}

add_action( 'elementor/widgets/register', 'register_mq_filler_card_widget' );


