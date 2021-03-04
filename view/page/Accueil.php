<!-- Slide image -->

<div class="slideshow-container">
    <div class="mySlides fade">
        <!--<div class="numbertext">1 / 3</div>-->
        <img class="img-fluid" src="resources/userContent/image_2.jpg" style="width:100%">
        <!--<div class="text">Caption Text</div>-->
    </div>
    <div class="mySlides fade">
        <!--<div class="numbertext">2 / 3</div>-->
        <img class="img-fluid" src="resources/userContent/image_4.jpg" style="width:100%">
        <!--<div class="text">Caption Two</div>-->
    </div>
    <div class="mySlides fade">
        <!--<div class="numbertext">3 / 3</div>-->
        <img class="img-fluid" src="resources/userContent/image_5.jpg" style="width:100%">
        <!--<div class="text">Caption Three</div>-->
    </div>
    <br>
    <div style="text-align:center">
        <span class="dot" onclick="currentSlide(1)"></span>
        <span class="dot" onclick="currentSlide(2)"></span>
        <span class="dot" onclick="currentSlide(3)"></span>
    </div>
</div>

<script>
    var slideIndex = 0;
    showSlides();

    function showSlides() {
        var i;
        var slides = document.getElementsByClassName("mySlides");
        var dots = document.getElementsByClassName("dot");
        for (i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
        }
        slideIndex++;
        if (slideIndex > slides.length) {slideIndex = 1}
        for (i = 0; i < dots.length; i++) {
            dots[i].className = dots[i].className.replace(" active", "");
        }
        slides[slideIndex-1].style.display = "block";
        dots[slideIndex-1].className += " active";
        setTimeout(showSlides, 4000); // Change image toute les 2 secondes
    }
</script>

<div class="container container-sm-fluid">

    <div class="textAccueil <?php if ($_SESSION['adminRight']) { echo "textAccueil-admin"; } ?> mb-4 pb-4">
        <h1 class="text-center mb-3">Menus végétariens de la semaine</h1>
        <div clase="container">
            <div class="row justify-content-around">
                <?php
                $meals = $_SESSION['meals'];
                $nbrMeal = count($meals);
                
                if($nbrMeal != 0){
                    for($x=0; $x < $nbrMeal; $x++){
                        if($x%2 == 0 && $x != 0){
                            ?>
                            </div>
                            <div class="row justify-content-around">
                            <?php
                        }
                        ?>
                        <div class="text-center my-1">
                            <p class="mb-0">Menu n°<?php echo($x + 1); ?></p>
                            <h3 class="py-0"><?php echo($meals[$x]['meaName']);?></h3>
                        </div>
                        <?php
                        }
                }
                else{
                    echo("Aucun menu disponible cette semaine cette semaine");
                }
                ?>
            </div>
        </div>
    </div>

    <div class="textAccueil <?php if ($_SESSION['adminRight']) { echo "textAccueil-admin"; } ?> mb-4">
        <h5>Des jeunes en formation préparent les repas à d'autres jeunes en formation </h5>
        <p> Des apprenti-e-s cuisiniers ou cuisinières et des apprenti-e-s gestionnaires en intendance du Centre d’Orientation et de Formations Professionnelles (COFOP) préparent les repas servis quotidiennement à l’ETML.</p>
        <p class="mb-0">Le projet est construit sur le modèle pédagogique du restaurant du COFOP. Il vise à répondre partiellement à la demande croissante de places d’apprentissage en cuisine et en intendance pour les jeunes et particulièrement dans le programme FORJAD (Formation pour jeunes adultes en difficulté). </p>
        <div class="text-center mb-2">
            <img class="img-fluid" src="resources/cofop.png">
        </div>
        <p>	Ce sont ainsi 18 places d’apprentissage qui sont proposées à l’ETML dans un environnement original et motivant: ces apprenti-e-s évoluent au contact d’autres jeunes en formation.</p>
        <p>	Concrètement, les apprenti-e-s sont placé-e-s sous la responsabilité du COFOP. Ils et elles sont formé-e-s par quatre professionnel-le-s, deux en cuisine et deux en intendance ainsi qu’une enseignante et un enseignant en branches générales. Ils suivent les cours professionnels respectivement à l'Ecole Professionnelle de Montreux et au Centre d'Enseignement des Métiers de l'Economie Familiale de Marcelin. </p>
        <p>	Le projet est porteur à plusieurs niveaux: conformément à sa volonté politique de permettre à chacun d'obtenir une formation certifiante, l'Etat offre 18 nouvelles places d'apprentissage dans ses propres locaux à des jeunes au parcours pas toujours facile ou en rupture d'apprentissage. Ils ou elles acquièrent leur formation dans un cadre scolaire, tout en bénéficiant d'infrastructures professionnelles. </p>
        <h5>Paiement</h5>
        <p>	Les paiements se font directement à la caisse par carte bancaire, postfinance card, TWINT (ou carte de légitimation - sur demande spéciale). La carte de légitimation peut être chargée uniquement à la caisse du restaurant, soit avec une carte bancaire ou avec du cash (montant minimum de CHF 20.-). </p>
        <p>	Il est toujours indispensable de présenter votre carte de légitimation lors de votre passage à la caisse.</p>
        <h5>Important </h5>
        <p>	Toute transaction en cash à la caisse du restaurant est majorée d'une taxe de CHF 2.- (paiement cash d'un repas ou d'un café par exemple).
            Annuellement le COFOP reverse le produit de cette taxe de 2.- sur le fonds d'encouragement de l'ETML.
            Le but de cette mesure est de répondre au mieux au plan de formation des intendants et d'améliorer la vitesse de passage aux caisses.	</p>
    </div>
</div>
