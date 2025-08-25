<?php

namespace App\Service;

use App\Entity\LegalInformation;
use Symfony\Component\Routing\RouterInterface;

class MentionsLegalesService
{
    public function __construct(
        private RouterInterface $routerInterface
    )
    {
        
    }
    public function mentionsParagraphs(LegalInformation $legales){

        $paragraphs = [
            [
            'title' => 'LIENS VERS D’AUTRES SITES',
            'text' => $legales->getCompanyName().' peut insérer sur le site des liens vers des sites internet tiers.<br/>'.
                    $legales->getCompanyName().' ne pourra être tenue responsable du fonctionnement et du contenu de ces sites, et des dommages pouvant être subis par tout utilisateur lors d’une visite de ces sites.<br/>
                    Des sites tiers peuvent également contenir des liens hypertextes vers le site.'
            ]
            ,
            [
            'title' => 'PROPRIÉTÉ INTELLECTUELLE',
            'text' => ' Le site et son contenu sont protégés en vertu du droit de la propriété intellectuelle.<br/>
                    Le logo de '.$legales->getCompanyName().', le nom commercial, ainsi que l’intégralité du contenu du site, sont la propriété exclusive de '.$legales->getCompanyName().', seule habilitée à utiliser les droits de propriété intellectuelle attachés. Toute reproduction totale ou partielle du site est strictement interdite sauf accord préalable de '.$legales->getCompanyName().'.<br/>
                    L’accès au site confère uniquement à l’utilisateur un droit d’usage privé et non exclusif du site. '.$legales->getCompanyName().' est libre de modifier, à tout moment et sans préavis, le contenu du site ainsi que les présentes mentions.<br/>
                    '.$legales->getCompanyName().' ne pourra être tenu responsable des conséquences de ces modifications.<br/>
                    Toute modification sera considérée comme étant acceptée sans réserve par l’utilisateur dès lors qu’il accèdera au site postérieurement à leur mise en ligne.'
            ]
            ,
            [
            'title' => 'UTILISATION DU SITE',
            'text' => 'Le site est accessible à tout utilisateur disposant d’un accès à internet.<br/>
            L’utilisateur est responsable de son équipement informatique, de son accès à internet et reconnaît avoir la compétence et les moyens adaptés pour utiliser le site.<br/>
            Tous les coûts relatifs à l’accès au site restent à la charge de l’utilisateur.'
            ]
            ,
            [
            'title' => 'INDISPONIBILITÉ DU SITE',
            'text' => ''.$legales->getCompanyName().' se réserve le droit d’interrompre ou de suspendre, à tout moment et sans préavis, tout ou partie du site.<br/>
            '.$legales->getCompanyName().' ne pourra, en aucune façon, être tenue responsable en cas d’indisponibilité du site pour quelque cause que ce soit.'
            ]
            ,
            [
            'title' => 'INFORMATIONS FIGURANT SUR LE SITE',
            'text' => 'Les informations et éléments figurant sur le site sont disponibles à des fins exclusivement d’information.<br/>'
            .$legales->getCompanyName().' fait son possible afin de contrôler la réalité de ces informations et de maintenir le site à jour.<br/> Toutefois, le contenu du site n’est en aucune façon garantie.'
            ]
            ,
            [
            'title' => 'RESPONSABILITÉ',
            'text' => $legales->getCompanyName().' ne peut, en aucune façon, être tenue responsable des dommages directs et/ou indirects qui résulteraient de l’utilisation ou de l’accès au site.<br/>'
            .$legales->getCompanyName().' ne saurait notamment voir sa responsabilité engagée en cas d’un dommage ou d’un virus qui pourrait infecter l’ordinateur de l’utilisateur ou son matériel informatique à la suite de l’accès ou de l’utilisation du site.'
            ]

        ];

        return $paragraphs;
    }
}