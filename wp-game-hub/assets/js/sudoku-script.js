jQuery(document).ready(function($) {
    let solution = [];
    const fill = $('#sudoku-game').data('fill');

    function generateFullBoard() {
        const board = Array.from({ length: 9 }, () => Array(9).fill(0));

        function isValid(board, row, col, num) {
            for (let i = 0; i < 9; i++) {
                if (
                    board[row][i] === num ||
                    board[i][col] === num ||
                    board[3 * Math.floor(row / 3) + Math.floor(i / 3)]
                        [3 * Math.floor(col / 3) + i % 3] === num
                ) {
                    return false;
                }
            }
            return true;
        }

        function fillBoard(board) {
            for (let row = 0; row < 9; row++) {
                for (let col = 0; col < 9; col++) {
                    if (board[row][col] === 0) {
                        let nums = [1, 2, 3, 4, 5, 6, 7, 8, 9].sort(() => Math.random() - 0.5);
                        for (let num of nums) {
                            if (isValid(board, row, col, num)) {
                                board[row][col] = num;
                                if (fillBoard(board)) return true;
                                board[row][col] = 0;
                            }
                        }
                        return false;
                    }
                }
            }
            return true;
        }

        fillBoard(board);
        return board;
    }

    function createPuzzle(board, blanks) {
        const puzzle = board.map(row => [...row]);
        let count = 0;
        while (count < blanks) {
            const row = Math.floor(Math.random() * 9);
            const col = Math.floor(Math.random() * 9);
            if (puzzle[row][col] !== 0) {
                puzzle[row][col] = 0;
                count++;
            }
        }
        return puzzle;
    }

    window.generateSudoku = function() {
        const sudokuContainer = $('#sudoku-game');
        sudokuContainer.empty(); 

        const fullBoard = generateFullBoard(); 
        // console.log(fullBoard);
        const puzzle = createPuzzle(fullBoard, 81 - fill); 
        solution = fullBoard;

        
        for (let blockRow = 0; blockRow < 3; blockRow++) { 
            const blockRowDiv = $('<div>').addClass('block-row'); 

            for (let blockCol = 0; blockCol < 3; blockCol++) { 
                const blockDiv = $('<div>').addClass('block'); 

                for (let row = blockRow * 3; row < (blockRow + 1) * 3; row++) {
                    const rowDiv = $('<div>').addClass('block-row-cells'); 

                    for (let col = blockCol * 3; col < (blockCol + 1) * 3; col++) {
                        const input = $('<input>')
                            .attr('type', 'text')
                            .attr('maxlength', 1)
                            .data('row', row)
                            .data('col', col);

                        if (puzzle[row][col] !== 0) {
                            input.val(puzzle[row][col]);
                            input.prop('disabled', true); 
                        }

                        
                        input.on('input', function() {
                            const val = parseInt($(this).val());
                            const correct = solution[row][col];
                            $(this).removeClass('correct incorrect');

                            if (!isNaN(val)) {
                                if (val === correct) {
                                    $(this).addClass('correct');
                                    $(this).prop('disabled', true); 
                                } else {
                                    $(this).addClass('incorrect');
                                }
                            }
                        });

                        rowDiv.append(input);
                    }

                    blockDiv.append(rowDiv); 
                }

                blockRowDiv.append(blockDiv); 
            }

            sudokuContainer.append(blockRowDiv); 
        }
    };


    window.resetSudoku = function() {
        $('#sudoku-game input').each(function() {
            if (!$(this).prop('disabled')) {
                $(this).val('').removeClass('correct incorrect');
            }
        });
    };

    $(document).ready(generateSudoku);
});
