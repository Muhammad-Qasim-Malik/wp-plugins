<?php
if (!defined('ABSPATH')) {
    exit; 
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Elementor_Sudoku_Widget extends Elementor\Widget_Base {

    // Get widget name
    public function get_name() {
        return 'gamehub_sudoku_widget';
    }

    // Get widget title
    public function get_title() {
        return 'Sudoku Game';
    }

    public function get_icon() {
        return 'eicon-counter';
    }

    public function get_categories() {
        return ['basic'];
    }

    protected function _register_controls() {

        /**************************************************************************************************/
        /*******************************************Content Section****************************************/
        /**************************************************************************************************/
        $this->start_controls_section(
            'section_settings',
            [
                'label' => __('Sudoku Settings', 'wp-game-hub'),
            ]
        );

        // Fill Control
        $this->add_control(
            'fill',
            [
                'label' => __('Number of Filled Cells', 'wp-game-hub'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 10,
                'min' => 1,
                'max' => 81,
                'step' => 1,
            ]
        );

        $this->end_controls_section();


        /**************************************************************************************************/
        /*********************************************Main Section*****************************************/
        /**************************************************************************************************/

        $this->start_controls_section(
            'main_section',
            [
                'label' => __('Main Container', 'wp-game-hub'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE, 
            ]
        );
        
        $this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'background',
				'types' => [ 'classic', 'gradient', 'video', 'image' ],
				'selector' => '{{WRAPPER}} #game-container',
			]
		);

        $this->add_control(
            'alignment',
            [
                'label' => __('Alignment', 'wp-game-hub'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'start' => [
                        'title' => __('Left', 'wp-game-hub'),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'wp-game-hub'),
                        'icon' => 'fa fa-align-center',
                    ],
                    'end' => [
                        'title' => __('Right', 'wp-game-hub'),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} #game-container' => 'align-items: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'main_padding',
            [
                'label' => __('Padding', 'wp-game-hub'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
                    'top' => '10',
                    'right' => '10',
                    'bottom' => '10',
                    'left' => '10',
                ],
                'selectors' => [
                    '{{WRAPPER}} #game-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'selector' => '{{WRAPPER}} #game-container',
			]
		);

        $this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_shadow',
				'selector' => '{{WRAPPER}} #game-container',
			]
		);

        $this->add_control(
            'main_radius',
            [
                'label' => __('Border Radius', 'wp-game-hub'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
                    'top' => '10',
                    'right' => '10',
                    'bottom' => '10',
                    'left' => '10',
                ],
                'selectors' => [
                    '{{WRAPPER}} #game-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'main_gap',
            [
                'label' => __('Gap Between Game & Button', 'wp-game-hub'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],  
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} #game-container' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();


        /**************************************************************************************************/
        /*********************************************Game Section*****************************************/
        /**************************************************************************************************/
        $this->start_controls_section(
            'Game_section',
            [
                'label' => __('Game Gap', 'wp-game-hub'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE, 
            ]
        );

        $this->add_control(
            'game_row_gap',
            [
                'label' => __('Gap Between Rows', 'wp-game-hub'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],  
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} #sudoku-game' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'game_col_gap',
            [
                'label' => __('Gap Between Columns', 'wp-game-hub'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],  
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .block-row' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->end_controls_section();

        /**************************************************************************************************/
        /*********************************************Block Section*****************************************/
        /**************************************************************************************************/
        $this->start_controls_section(
            'block_section',
            [
                'label' => __('Block', 'wp-game-hub'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE, 
            ]
        );

        $this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'block_background',
				'types' => [ 'classic', 'gradient', 'video', 'image' ],
				'selector' => '{{WRAPPER}} .block',
			]
		);

        $this->add_control(
            'block_padding',
            [
                'label' => __('Padding', 'wp-game-hub'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
                    'top' => '10',
                    'right' => '10',
                    'bottom' => '10',
                    'left' => '10',
                ],
                'selectors' => [
                    '{{WRAPPER}} .block' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'block_border',
				'selector' => '{{WRAPPER}} .block',
			]
		);

        $this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'block_box_shadow',
				'selector' => '{{WRAPPER}} .block',
			]
		);

        $this->add_control(
            'block_radius',
            [
                'label' => __('Border Radius', 'wp-game-hub'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
                    'top' => '10',
                    'right' => '10',
                    'bottom' => '10',
                    'left' => '10',
                ],
                'selectors' => [
                    '{{WRAPPER}} .block' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'block_row_gap',
            [
                'label' => __('Gap Between Rows', 'wp-game-hub'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],  
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .block' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'block_col_gap',
            [
                'label' => __('Gap Between Rows', 'wp-game-hub'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],  
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .block-row-cells' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        /**************************************************************************************************/
        /********************************************Input Section*****************************************/
        /**************************************************************************************************/

        $this->start_controls_section(
            'input_section',
            [
                'label' => __('Input', 'wp-game-hub'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE, 
            ]
        );

        // General Border Radius Control
        $this->add_control(
            'input_border_radius',
            [
                'label' => __('Border Radius', 'wp-game-hub'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
                    'top' => '5',
                    'right' => '5',
                    'bottom' => '5',
                    'left' => '5',
                ],
                'selectors' => [
                    '{{WRAPPER}} input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Simple Input Styling (Unmodified Inputs)
        $this->add_control(
            'simple_background_color',
            [
                'label' => __('Simple Background Color', 'wp-game-hub'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#f9f9f9',
                'selectors' => [
                    '{{WRAPPER}} input:not(.correct):not(.incorrect):not(:disabled)' => 'background-color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'simple_color',
            [
                'label' => __('Simple Text Color', 'wp-game-hub'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#333',
                'selectors' => [
                    '{{WRAPPER}} input:not(.correct):not(.incorrect):not(:disabled)' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        // Simple Border Control for Unmodified Inputs
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'simple_border',
                'selector' => '{{WRAPPER}} input:not(.correct):not(.incorrect):not(:disabled)',
            ]
        );

        // Correct Input Styling
        $this->add_control(
            'correct_background_color',
            [
                'label' => __('Correct Background Color', 'wp-game-hub'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#c8f7c5',
                'selectors' => [
                    '{{WRAPPER}} .correct' => 'background-color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'correct_color',
            [
                'label' => __('Correct Text Color', 'wp-game-hub'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#2d6a4f',
                'selectors' => [
                    '{{WRAPPER}} .correct' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        // Correct Border Control
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'correct_border',
                'selector' => '{{WRAPPER}} .correct',
            ]
        );

        // Incorrect Input Styling
        $this->add_control(
            'incorrect_background_color',
            [
                'label' => __('Incorrect Background Color', 'wp-game-hub'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#fbc7c7',
                'selectors' => [
                    '{{WRAPPER}} .incorrect' => 'background-color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'incorrect_color',
            [
                'label' => __('Incorrect Text Color', 'wp-game-hub'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#9d3d3d',
                'selectors' => [
                    '{{WRAPPER}} .incorrect' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        // Incorrect Border Control
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'incorrect_border',
                'selector' => '{{WRAPPER}} .incorrect',
            ]
        );

        // Filled Input (Disabled) Styling
        $this->add_control(
            'filled_background_color',
            [
                'label' => __('Filled Background Color', 'wp-game-hub'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#f0f0f0',
                'selectors' => [
                    '{{WRAPPER}} input:disabled:not(.correct):not(.incorrect)' => 'background-color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'filled_color',
            [
                'label' => __('Filled Text Color', 'wp-game-hub'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#888',
                'selectors' => [
                    '{{WRAPPER}} input:disabled:not(.correct):not(.incorrect)' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        // Filled Border Control
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'filled_border',
                'selector' => '{{WRAPPER}} input:disabled:not(.correct):not(.incorrect)',
            ]
        );

        $this->add_control(
            'focus_outline_color',
            [
                'label' => __('Focus Outline Color', 'wp-game-hub'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'white',
                'selectors' => [
                    '{{WRAPPER}} input:focus' => 'outline: 2px solid {{VALUE}} !important;',  // Use outline instead of border
                ],
            ]
        );

        $this->end_controls_section();

        /**************************************************************************************************/
        /********************************************Button Section****************************************/
        /**************************************************************************************************/

        $this->start_controls_section(
            'button_section',
            [
                'label' => __('Button Styling', 'wp-game-hub'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE, 
            ]
        );

        // Button Background Color
        $this->add_control(
            'button_background_color',
            [
                'label' => __('Button Background Color', 'wp-game-hub'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#3b82f6', // Default color
                'selectors' => [
                    '{{WRAPPER}} .gamehubbutton' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        // Button Text Color
        $this->add_control(
            'button_text_color',
            [
                'label' => __('Button Text Color', 'wp-game-hub'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff', 
                'selectors' => [
                    '{{WRAPPER}} .gamehubbutton' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Button Padding
        $this->add_control(
            'button_padding',
            [
                'label' => __('Button Padding', 'wp-game-hub'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
                    'top' => '10',
                    'right' => '20',
                    'bottom' => '10',
                    'left' => '20',
                ],
                'selectors' => [
                    '{{WRAPPER}} .gamehubbutton' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'button_border',
                'selector' => '{{WRAPPER}} .gamehubbutton',
            ]
        );

        $this->add_control(
            'button_border_radius',
            [
                'label' => __('Button Border Radius', 'wp-game-hub'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
                    'top' => '5',
                    'right' => '5',
                    'bottom' => '5',
                    'left' => '5',
                ],
                'selectors' => [
                    '{{WRAPPER}} .gamehubbutton' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Button Hover Background Color
        $this->add_control(
            'button_hover_background_color',
            [
                'label' => __('Button Hover Background Color', 'wp-game-hub'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#2563eb', 
                'selectors' => [
                    '{{WRAPPER}} .gamehubbutton:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        // Button Hover Text Color
        $this->add_control(
            'button_hover_text_color',
            [
                'label' => __('Button Hover Text Color', 'wp-game-hub'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff', 
                'selectors' => [
                    '{{WRAPPER}} .gamehubbutton:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Button Hover Border
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'button_hover_border',
                'selector' => '{{WRAPPER}} .gamehubbutton:hover',
            ]
        );

        $this->end_controls_section();


    }

    // Render the widget content
    protected function render() {
        $settings = $this->get_settings_for_display();

    $fill = isset($settings['fill']) ? $settings['fill'] : 10;

        ?>
        <div id="game-container">
            <div id="sudoku-game" data-fill="<?php echo esc_attr($fill); ?>"></div>
            <div id="game-container-actions">
                <button onclick="generateSudoku()" class="gamehubbutton">New Game</button>
                <button onclick="resetSudoku()" class="gamehubbutton">Reset</button>
            </div>
        </div>
        <?php
    }
}
