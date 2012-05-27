<script type="text/javascript">
    $(function(){
        // Tabs
        $('#groupes').tabs();
    });

    function modifyBet(idMatch){
        $(document).ready(function() {
            var idMatchScoreInput = '#matchScoreInput_'+idMatch;
            var idMatchScore = '#matchScore_'+idMatch;
		
            $(idMatchScoreInput).css("display","inline-block");
            $(idMatchScore).hide();
        });
    }

    function saveBet(idMatch){
        $(document).ready(function() {
            var idMatchScoreInput = '#matchScoreInput_'+idMatch;
            var idMatchScore = '#matchScore_'+idMatch;

            var scoreA = $('#scoreA_match_'+idMatch).val();
            var scoreB = $('#scoreB_match_'+idMatch).val();

            // AJAX ici pour modifier les scores
            // $.post("modifier_score.php", { idMatch: idMatch, scoreA: scoreA, scoreB: scoreB} );
		
            $(idMatchScoreInput).hide();

            $(idMatchScore).html('Pari : '+scoreA +' - '+scoreB);
            $(idMatchScore).css("display","inline-block");
        });
    }
</script>

<!-- Tabs -->
<div id="groupes">
    <ul>
        <?php
        $groups = Group::findAll();

        for($i = 0; $i < sizeof($groups);$i++){
            echo '<li><a href="#groupe'.$groups[$i]->getId().'">'.$groups[$i]->getTitle().'</a></li>';
        }
        ?>
    </ul>
        <?php
        for($i = 0; $i < sizeof($groups);$i++){
                echo '<div id="groupe'.$groups[$i]->getId().'">'
                    // DISPLAY MATCH HERE 
                .'</div>';
            }
            
//
//        <div class="match">
//            <span class="matchDate">lun 11/06/2012 : 18h00</span>
//            <span class="matchTeam">FRANCE</span>
//            <span class="matchScoreEnd win">
//                Résultat : 4 - 0 // Pari : 4 - 0 <img src="includes/pictures/exact.png"  width="16" height="16" alt ="exact" title="Bonus pari exact"/>
//            </span>
//            <span class="matchTeam">ANGLETERRE</span>
//        </div>
//        <div class="match">
//            <span class="matchDate">lun 11/06/2012 : 20h45</span>
//            <span class="matchTeam">UKRAINE</span>
//            <span class="matchScoreEnd loose">
//                Résultat : 1 - 1 // Pari : 0 - 1
//            </span>
//            <span class="matchTeam">SUEDE</span>
//        </div>
//        <div class="match">
//            <span class="matchDate">ven 15/06/2012 : 18h00</span>
//            <span class="matchTeam">UKRAINE</span>
//            <span class="matchScoreEnd">En cours // Pari : 1 - 4</span>
//            <span class="matchTeam">FRANCE</span>
//        </div>
//        <div class="match">
//            <span class="matchDate">ven 15/06/2012 : 20h45</span>
//            <span class="matchTeam">SUEDE</span>
//            <span id="matchScore_3" class="matchScore" title="Modifier le paris" onclick="modifyBet(3)"> - </span>
//            <span id="matchScoreInput_3" class="matchScoreInput">
//                <select id="scoreA_match_3" name="scoreA_match_3">
//                    <option value="0">0</option>
//                    <option value="1">1</option>
//                    <option value="2">2</option>
//                    <option value="3">3</option>
//                    <option value="4">4</option>
//                    <option value="5">5</option>
//                    <option value="6">6</option>
//                    <option value="7">7</option>
//                    <option value="8">8</option>
//                    <option value="9">9</option>
//                    <option value="10">10</option>
//                </select>
//                -
//                <select id="scoreB_match_3" name="scoreB_match_3">
//                    <option value="0">0</option>
//                    <option value="1">1</option>
//                    <option value="2">2</option>
//                    <option value="3">3</option>
//                    <option value="4">4</option>
//                    <option value="5">5</option>
//                    <option value="6">6</option>
//                    <option value="7">7</option>
//                    <option value="8">8</option>
//                    <option value="9">9</option>
//                    <option value="10">10</option>
//                </select>
//
//                <button id="saveButton" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"
//                        role="button" aria-disabled="false" onclick="saveBet(3)">
//                    <span class="ui-button-text">Valider</span>
//                </button>
//            </span>
//            <span class="matchTeam">ANGLETERRE</span>
//        </div>
//        <div class="match">
//            <span class="matchDate">mar 19/06/2012 : 20h45</span>
//            <span class="matchTeam">SUEDE</span>
//            <span class="matchScore" title="Modifier le paris"> - </span>
//            <span class="matchTeam">FRANCE</span>
//        </div>
//        <div class="match">
//            <span class="matchDate">mar 19/06/2012 : 20h45</span>
//            <span class="matchTeam">ANGLETERRE</span>
//            <span class="matchScore" title="Modifier le paris"> - </span>
//            <span class="matchTeam">UKRAINE</span>
//        </div>
    ?>
</div>