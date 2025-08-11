<?php
if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Controls_Manager;

class MQ_Heading_Widget extends Widget_Base {

    public function get_name() {
        return 'mq_heading';
    }

    public function get_title() {
        return __( 'MQ Heading', 'mq-elementor-widgets' );
    }

    public function get_icon() {
        return 'eicon-heading';
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
            'heading_text',
            [
                'label'   => __( 'Heading Text', 'mq-elementor-widgets' ),
                'type'    => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => __( 'Add Your Heading Text Here', 'mq-elementor-widgets' ),
            ]
        );

        $this->add_control(
            'heading_tag',
            [
                'label'   => __( 'HTML Tag', 'mq-elementor-widgets' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'h2',
                'options' => [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                    'p'  => 'Paragraph (p)',
                ],
            ]
        );

        $this->add_control(
            'heading_link',
            [
                'label' => __( 'Link', 'mq-elementor-widgets' ),
                'type'  => Controls_Manager::URL,
                'dynamic' => [
                    'active' => true,
                ],
                'placeholder' => __( 'https://your-link.com', 'mq-elementor-widgets' ),
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

        $this->add_control(
            'heading_color',
            [
                'label'     => __( 'Heading and Link Color', 'mq-elementor-widgets' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mq-heading' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .mq-heading a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'      => 'content_typography',
                'selector'  => '{{WRAPPER}} .mq-heading',
            ]
        );

        $this->add_control(
            'heading_alignment',
            [
                'label'     => __( 'Alignment', 'mq-elementor-widgets' ),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => [
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
                'default'   => 'center',
                'selectors' => [
                    '{{WRAPPER}} .mq-heading' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Hover Section
        $this->start_controls_section(
            'hover_section',
            [
                'label' => __( 'Link Hover', 'mq-elementor-widgets' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'heading_link_hover_color',
            [
                'label'     => __( 'Link Hover Color', 'mq-elementor-widgets' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mq-heading a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'      => 'link_hover_typography',
                'selector'  => '{{WRAPPER}} .mq-heading a:hover',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        $tag = $settings['heading_tag'];
        $heading_text = $settings['heading_text'];
        $heading_link = $settings['heading_link']['url'] ? $settings['heading_link']['url'] : '';

        echo '<' . esc_attr( $tag ) . ' class="mq-heading"';
        if ( ! empty( $heading_link ) ) {
            echo '><a href="' . esc_url( $heading_link ) . '" target="_blank">' . esc_html( $heading_text ) . '</a>';
        } else {
            echo '>' . esc_html( $heading_text ) . '</' . esc_attr( $tag ) . '>';
        }
    }
}

function register_mq_heading_widget( $widgets_manager ) {
    require_once( __DIR__ . '/heading-widget.php' );
    $widgets_manager->register( new \MQ_Heading_Widget() );
}

add_action( 'elementor/widgets/register', 'register_mq_heading_widget' );
