<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title> HANGMAN GAME </title>
    <link rel="stylesheet" href="design.css"/>
</head>

<body onload="startGame()">
    <?php
        //set boolean to initiate start of hangman game.
        $hangmanStart = "true";
        $_SESSION["gameWinner"] = "false";  

        //set a variable with index value of the hint and make sure it remains the same after user is guessing
        if(isset($_POST["hiddenNum"])){
            $hintIndex = $_POST["hiddenNum"];
        } else {
            if(isset($_POST["hint"])){
                $hintIndex  = $_POST["hint"];
            }
        }

        //set a variable with the hidden word and make sure it remains the same after user tries to guess.
        if(isset($_POST["hiddenWord"])){
            $passedWord = strtolower($_POST["hiddenWord"]);
        } else {
            if(isset($_POST["word"])){
                $passedWord = strtolower($_POST["word"]);
            }
        }
        
        //set hint array to match the hidden word that was randomly selected.
        $hintArray = array("A delicious triangle", "One that creates websites", "Coding language used to build websites", "A device you\'re currently using.", "A process of creating computer programs.");
        $definition = $hintArray[intval($hintIndex)];            
    ?>
    <input type="hidden" id='word_definition' value='<?php if(strlen($definition) > 0) { echo $definition; } else { echo $noHint; } ?>'/>
    <div id="titlePortion">
        <h2> Instructions:</h2> 
        <h3> Click into the textbox below and enter <span style="color:black; text-transform:uppercase;">one</span> letter to piece together the word. <br> If you need a hint, click the <span style="color:yellow;">'Hint'</span> button and, maybe, it'll help you out. Or, if you think this game sucks and don't want to play anymore, hit the <span style="color:blue;">'Give Up'</span> button and it will tell you the word. <br> Have fun playing!</h3>  
    </div>

    <br><br>
                                   
    <div id="gameStatus">
        <table>
            <thead class="statusDesign">
                <tr>
                    <td>Incorrect Guesses</td>
                    <td>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</td>
                    <td>Correct Guesses</td>
                    <td>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</td>
                    <td>Guesses Left</td>
                </tr>
            </thead>
            <tbody class="statusDesign">
                <tr>
                    <td>
                        <span style="color:rgba(131, 0, 0, 0.795);">
                            <?php
                                //checks user guesses to see if only letters are entered and checks to see if user guess is incorrect.
                                //If guess is incorrect, guess is placed in a "bad word" array.
                                if(isset($_POST["userGuess"])){                              
                                    if(preg_match("/[A-Za-z-']/", strtolower($_POST["userGuess"]))){                                   
                                        if(strpos($passedWord, strtolower($_POST["userGuess"])) == false){
                                            if(empty($_SESSION["badWords"])){
                                                $_SESSION["badWords"][] = strtolower($_POST["userGuess"]);
                                            } else {
                                                if(!in_array(strtolower($_POST["userGuess"]), $_SESSION["badWords"])){
                                                    $_SESSION["badWords"][] = strtolower($_POST["userGuess"]);
                                                } else {
                                                    if(isset($_POST["guessNumber"])){                             
                                                       $_POST["guessNumber"]+= 1;
                                                    }
                                                    echo "<script type='text/javascript'>alert('HEY! YOU ALREADY ENTERED THAT'); document.getElementById('answerBox').focus(); document.getElementById('answerBox').value = ''; </script>";
                                                }
                                            }                              
                                            echo implode(", ", $_SESSION["badWords"]);                                                 
                                        } else {
                                            if(empty($_SESSION["badWords"])){
                                                echo "";
                                            } else {
                                                echo implode(", ", $_SESSION["badWords"]);
                                            } 
                                        }                                 
                                    } 
                                }                         
                            ?>
                        </span>
                    </td>
                    <td></td>
                    <td>
                        <span style="color:rgb(46, 214, 46);">
                            <?php
                                 //checks user guesses to see if only letters are entered and checks to see if user guess is incorrect.
                                //If guess is correct, guess is placed in a "good word" array.
                                if(isset($_POST["userGuess"])){                                   
                                    if(preg_match("/[A-Za-z-']/", strtolower($_POST["userGuess"]))){
                                        if(strpos($passedWord, strtolower($_POST["userGuess"])) !== false){
                                            if(empty($_SESSION["goodWords"])){
                                                $_SESSION["goodWords"][] = strtolower($_POST["userGuess"]); 
                                            } else {
                                                if(!in_array(strtolower($_POST["userGuess"]), $_SESSION["goodWords"])){
                                                    $_SESSION["goodWords"][] = strtolower($_POST["userGuess"]);
                                                } else {
                                                    echo "<script type='text/javascript'>alert('HEY! YOU ALREADY ENTERED THAT'); document.getElementById('answerBox').focus(); document.getElementById('answerBox').value = ''; </script>";
                                                }
                                            }
                                            echo implode(", ", $_SESSION["goodWords"]);
                                        } else {
                                            if(empty($_SESSION["goodWords"])){
                                                echo "";
                                            } else {
                                                echo implode(", ", $_SESSION["goodWords"]);  
                                            }
                                        }                           
                                    }    
                                } 
                            ?>
                        </span>
                    </td>
                    <td></td>
                    <td>
                        <span id="guessCount">
                            <?php
                                //counts down the amount of numbers a user has left.  
                                if(isset($_POST["guessNumber"])){                              
                                    $numberOfGuesses = $_POST["guessNumber"];  
                                    if($numberOfGuesses == 0){
                                        session_destroy();
                                    }
                                } else {
                                    session_unset();                 
                                    $numberOfGuesses = 6; 
                                }
                                echo $numberOfGuesses;                                                                                                
                            ?>
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <br>

    <!--outputs hangman display here-->
    <div id="hangmanDisplay" class="divMargin" style="border:solid;background-color:whitesmoke;"> 
        <div class="platform">
            <pre>
                ======================
                |                    |
                |<span id="manHead" style="margin-left:26.1%; visibility:hidden;">O</span>
                |<span id="man_leftArm" style="margin-left:23.5%; visibility:hidden;">--</span><span id="manBody" style="margin-left; visibility:hidden;">|</span><span id="man_rightArm" style="visibility:hidden;">--</span> 
                |<span id="man_leftLeg" style="margin-left:25.5%; visibility:hidden;">/</span><span id="man_rightLeg" style="visibility:hidden;">\</span>
                |
                |
                |
                |
                =============\                /============               
            </pre>
        </div>
        <pre class="fish">
                |\_______/\________                  _________/\________/|
                | ____           .\ \              / /.              ___ |
                |/    \_____________/              \________________/   \|
        </pre>           
    </div>
    
    <div id="lastChance" style="display:none;">
        <h1 style="color:black;">LAST CHANCE!<h1>
    </div>

    <div id="wordDisplay" style="text-align:center;">
        <p>
            <?php
                //reveals correct letters the user has guessed correctly as the user plays and hides the remaining unknown letters.
                if(!isset($_SESSION["splitWord"])){
                    $_SESSION["splitWord"] = $passedWord;
                    for($i = 0; $i < strlen($passedWord); $i++){
                        $_SESSION["splitWord"][$i] = "_";
                    }   
                } else {
                    for($i = 0; $i < strlen($passedWord); $i++){
                        if(isset($_POST["userGuess"])){
                            if($passedWord[$i] == strtolower($_POST["userGuess"])){
                                $_SESSION["splitWord"][$i] = strtolower($_POST["userGuess"]); 
                            } 
                        } else {
                            $_SESSION["splitWord"][$i] = "_";
                        }
                    }  
                }
            ?>
            <?php
                if($_SESSION["splitWord"] === $passedWord){        
                    $_SESSION["gameWinner"] = "true";   
            ?>
                <div id="winnerDiv">
                    <h1> Winner winner chicken dinner! <br/> Dang! You're a genius! This game is too easy for you!</h1>
                        <img src="https://media.giphy.com/media/l0K44HVFzVDm92Di8/giphy.gif" alt="highfive">
                        <h1> The hidden word was: <?php for($x = 0; $x < strlen($_SESSION["splitWord"]); $x++){ echo $_SESSION["splitWord"][$x];}?>!</h1>                            
                        <br/><br/>
                        <a href="welcomePage.html"><button id="restartBtn" class="hintButton">Play again!</button></a>
                        <br/><br/>
                </div>
            <?php } else {  
                    for($x = 0; $x < strlen($_SESSION["splitWord"]); $x++){
                        echo $_SESSION["splitWord"][$x] . " ";
                    }
                    echo "( " . strlen($passedWord) . " letters )";
                }
            ?>
        </p>
    </div>
    <br>
    <div id="guessPortion" style="text-align:center;">
        <label for="answerBox">Guess Box: </label>
        <p style="font-size:15px; color:black; font-weight:700;">Please enter ONE letter at a time. Thanks!</p>
        <form id="guessSubmit" action="#" method="POST" onsubmit="return submitGuess()">
            <input type="text" id="answerBox" class="answerBoxSize" name="userGuess" /> <input type="submit" class="submitButton" value="GO!"/>
            <br><br><br>
            <input type="hidden" id="gameWord" name="word" value="<?php echo $passedWord?>"/>
            <input type="hidden" id="gameStart" name="gameStatus" value="<?php echo $hangmanStart?>"/>
            <input type="hidden" id="guessCounter" name="guessNumber" value="<?php echo $numberOfGuesses; ?>" />    
            <input type="hidden" id="userGuessing" name="guessingUser" value="<?php echo $_SESSION['splitWord']; ?>"/>
            <input type="hidden" id="hintNumber" name="hint" value="<?php echo $hintIndex;?>"/>
        </form>
        <input type="hidden" id="hiddenWinner" name="winner" value='<?php if(isset($_SESSION["gameWinner"])) { echo $_SESSION["gameWinner"]; } else { echo "false"; }  ?>'/>
        <button class="hintButton" onclick="giveHint()">Hint?</button> &emsp; <button class="quitButton" onclick="gameOver()">I give up</button>
    </div>

    <div id="loserDiv" style="text-align:center; display:none;">
        <h1> Awww shucks...you'll get it next time. </h1>
        <img src="https://media.giphy.com/media/QPUg0x3VGlg6Q/giphy.gif" alt="jake the dog"/>
        <h1> The hidden word was: <?php echo $passedWord; ?> </h1>
        <a href="welcomePage.html"><button id="restartBtn" class="hintButton">Play again?</button></a>
    </div>
</body>

    <script>
            let oldWord = document.getElementById("gameWord").value;
                
            function startGame() {
                document.getElementById("loserDiv").style.display="none";            
                document.getElementById("answerBox").focus();               
                var counter = document.getElementById("guessCounter").value; 
                checkWinner();
                if(counter == 5 ){
                    document.getElementById("manHead").style.visibility="visible";
                    document.getElementById("manBody").style.visibility="hidden";
                    document.getElementById("man_leftArm").style.visibility="hidden";
                    document.getElementById("man_rightArm").style.visibility="hidden";
                    document.getElementById("man_leftLeg").style.visibility="hidden";
                    document.getElementById("man_rightLeg").style.visibility="hidden";

                } else if(counter == 4) {
                    document.getElementById("manHead").style.visibility="visible";
                    document.getElementById("manBody").style.visibility="visible";
                    document.getElementById("man_leftArm").style.visibility="hidden";
                    document.getElementById("man_rightArm").style.visibility="hidden";
                    document.getElementById("man_leftLeg").style.visibility="hidden";
                    document.getElementById("man_rightLeg").style.visibility="hidden"; 
                } else if(counter == 3) {
                    document.getElementById("manHead").style.visibility="visible";
                    document.getElementById("manBody").style.visibility="visible";
                    document.getElementById("man_leftArm").style.visibility="visible";
                    document.getElementById("man_rightArm").style.visibility="hidden";
                    document.getElementById("man_leftLeg").style.visibility="hidden";
                    document.getElementById("man_rightLeg").style.visibility="hidden"; 

                } else if(counter == 2) {
                    document.getElementById("manHead").style.visibility="visible";
                    document.getElementById("manBody").style.visibility="visible";
                    document.getElementById("man_leftArm").style.visibility="visible";
                    document.getElementById("man_rightArm").style.visibility="visible";
                    document.getElementById("man_leftLeg").style.visibility="hidden";
                    document.getElementById("man_rightLeg").style.visibility="hidden";

                } else if(counter == 1) { 
                    document.getElementById("lastChance").style.display="block";                    
                    document.getElementById("manHead").style.visibility="visible";
                    document.getElementById("manBody").style.visibility="visible";
                    document.getElementById("man_leftArm").style.visibility="visible";
                    document.getElementById("man_rightArm").style.visibility="visible";
                    document.getElementById("man_leftLeg").style.visibility="visible";
                    document.getElementById("man_rightLeg").style.visibility="hidden";
                } else if(counter == 0) {
                    gameOver();
                    document.getElementById("manHead").style.visibility="visible";
                    document.getElementById("manBody").style.visibility="visible";
                    document.getElementById("man_leftArm").style.visibility="visible";
                    document.getElementById("man_rightArm").style.visibility="visible";
                    document.getElementById("man_leftLeg").style.visibility="visible";
                    document.getElementById("man_rightLeg").style.visibility="visible";
                   
                } else {
                    document.getElementById("manHead").style.visibility="hidden";
                    document.getElementById("manBody").style.visibility="hidden";
                    document.getElementById("man_leftArm").style.visibility="hidden";
                    document.getElementById("man_rightArm").style.visibility="hidden";
                    document.getElementById("man_leftLeg").style.visibility="hidden";
                    document.getElementById("man_rightLeg").style.visibility="hidden"; 
                }
            }
        
            function submitGuess(){ 
                var regexTester = /[A-Za-z-']/gi;
                document.getElementById("gameWord").value = oldWord;
                var userGuess =  document.getElementById("answerBox").value;
            
                if(regexTester.test(userGuess.toLowerCase())){
                    if(userGuess.toLowerCase().length > 1){
                        alert("WHOA! WHOA! TOO MANY LETTERS! ONLY ONE LETTER PLEASE!");
                        document.getElementById("answerBox").value='';
                        document.getElementById("answerBox").focus();
                        return false;
                    } else if ( userGuess.toLowerCase().length == 1){
                        if(oldWord.indexOf(userGuess.toLowerCase()) < 0){
                            var guesses = document.getElementById("guessCounter").value ;
                            guesses--;
                            document.getElementById("guessCounter").value = guesses;    
                            return true;
                        } 
                    } 
                } else {
                    alert("The guess you have entered is invalid. Please enter another guess."); 
                    document.getElementById("answerBox").value='';
                    document.getElementById("answerBox").focus();
                    return false;
                }
            }
        
            function giveHint(){
                var wordHint = document.getElementById("word_definition").value;
                alert("Hint, hint: " + "\n\n" + wordHint);
            }
        
            function gameOver(){  
                //this function should do the same for game over (if user got all wrong) & on give up.
                document.getElementById("gameStatus").style.display="none";
                document.getElementById("guessPortion").style.display="none";
                document.getElementById("titlePortion").style.display="none";
                document.getElementById("hangmanDisplay").style.display="none";
                document.getElementById("wordDisplay").style.display="none";
                document.getElementById("loserDiv").style.display = "block";
            }

            function checkWinner(){
                var winner = document.getElementById("hiddenWinner").value;
                if(winner == "true"){
                    document.getElementById("lastChance").style.display="none";
                    document.getElementById("gameStatus").style.display="none";
                    document.getElementById("guessPortion").style.display="none";
                    document.getElementById("titlePortion").style.display="none";
                    document.getElementById("hangmanDisplay").style.display="none";
                    document.getElementById("gameStatus").style.display="none";
                }
            }
            //User can press enter to enter guesses.
            var enterEvent = document.getElementById("answerBox");
            enterEvent.addEventListener("keyup", function(event){
                event.preventDefault();
                if(event.keyCode === 13){
                    document.getElementById("submitBtn").click();
                }
            });    
    </script>

</html>

