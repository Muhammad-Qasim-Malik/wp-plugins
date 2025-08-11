<?php

function gamehub_sudoku_shortcode($atts) {
    $atts = shortcode_atts(
        array(
            'fill' => 10, 
        ), 
        $atts, 
        'sudoku_game'
    );

    $fill = intval($atts['fill']); 

    ob_start();
    ?>
    <div id="game-container">
        <div id="sudoku-game" data-fill="<?php echo esc_attr($fill); ?>"></div>
        <div id="game-container-actions">
            <button onclick="generateSudoku()" class="gamehubbutton">New Game</button>
            <button onclick="resetSudoku()" class="gamehubbutton">Reset</button>
        </div>
    </div>

    <?php

    return ob_get_clean();
}
add_shortcode('sudoku_game', 'gamehub_sudoku_shortcode');