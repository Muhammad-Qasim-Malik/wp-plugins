<?php
if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Controls_Manager;

class MQ_Button_Widget extends Widget_Base {
     public function get_name() {
        return 'mq_button';
    }

    public function get_title() {
        return __( 'MQ Button', 'mq-elementor-widgets' );
    }

    public function get_icon() {
        return 'eicon-button';
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
            'button_text',
            [
                'label'   => __( 'Button Text', 'mq-elementor-widgets' ),
                'type'    => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => __( 'Click me', 'mq-elementor-widgets' ),
            ]
        );

        $this->add_control(
            'button_link',
            [
                'label' => __( 'Link', 'mq-elementor-widgets' ),
                'type'  => Controls_Manager::URL,
                'dynamic' => [
                    'active' => true,
                ],
                'placeholder' => __( 'https://your-link.com', 'mq-elementor-widgets' ),
            ]
        );
         $this->add_control(
            'button_alignment',
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
                    '{{WRAPPER}} .elementor-widget-container' => 'text-align: {{VALUE}};',
                    '{{WRAPPER}} .mq-button' => 'display: inline-block;',
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

        $this->add_control(
            'button_text_color',
            [
                'label'     => __( 'Text Color', 'mq-elementor-widgets' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mq-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_bg_color',
            [
                'label'     => __( 'Background Color', 'mq-elementor-widgets' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mq-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'      => 'button_typography',
                'selector'  => '{{WRAPPER}} .mq-button',
            ]
        );

        $this->add_control(
            'button_padding',
            [
                'label'      => __( 'Padding', 'mq-elementor-widgets' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .mq-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'button_radius',
            [
                'label'      => __( 'Border Radius', 'mq-elementor-widgets' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .mq-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'hover_section',
            [
                'label' => __( 'Button Hover', 'mq-elementor-widgets' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'button_hover_color',
            [
                'label'     => __( 'Hover Text Color', 'mq-elementor-widgets' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mq-button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_bg_color',
            [
                'label'     => __( 'Hover Background Color', 'mq-elementor-widgets' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .mq-button:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        $button_text = $settings['button_text'];
        $button_link = $settings['button_link']['url'] ? $settings['button_link']['url'] : '#';

        echo '<a href="' . esc_url( $button_link ) . '" class="mq-button">' . esc_html( $button_text ) . '</a>';
    }

}

function register_mq_button_widget( $widgets_manager ) {
    require_once( __DIR__ . '/button-widget.php' );
    $widgets_manager->register( new \MQ_Button_Widget() );
}

add_action( 'elementor/widgets/register', 'register_mq_button_widget' );