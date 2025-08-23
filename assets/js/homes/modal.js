// js de la modal pour les details d'un logement
document.addEventListener('DOMContentLoaded', () => {
    const homeDetailsModal = document.getElementById('homeDetailsModal');
    const modalTime = homeDetailsModal.querySelector('#modalTimeTravel');
    const modalEquipments = homeDetailsModal.querySelector('#modalEquipments');
    
    // Nouveaux sélecteurs pour les autres propriétés
    const modalDescription = homeDetailsModal.querySelector('#modalDescription');
    const modalType = homeDetailsModal.querySelector('#modalType');
    const modalSurface = homeDetailsModal.querySelector('#modalSurface');
    const modalRooms = homeDetailsModal.querySelector('#modalRooms');
    const modalBedrooms = homeDetailsModal.querySelector('#modalBedrooms');
    const modalBathrooms = homeDetailsModal.querySelector('#modalBathrooms');
    const modalFloor = homeDetailsModal.querySelector('#modalFloor');
    const modalHasElevator = homeDetailsModal.querySelector('#modalHasElevator');
    const modalHasBalcony = homeDetailsModal.querySelector('#modalHasBalcony');
    const modalOtherRules = homeDetailsModal.querySelector('#modalOtherRules');
    const modalHasGarage = homeDetailsModal.querySelector('#modalHasGarage');
    const modalHasParking = homeDetailsModal.querySelector('#modalHasParking');
    const modalTypeOfGarage = homeDetailsModal.querySelector('#modalTypeOfGarage');
    const modalTypeOfParking = homeDetailsModal.querySelector('#modalTypeOfParking');

    homeDetailsModal.addEventListener('show.bs.modal', async event => {
        const button = event.relatedTarget;
        const homeId = button.getAttribute('data-home-id');
        
        // Réinitialiser les contenus de la modale en attendant la réponse
        modalTime.textContent = 'Chargement...';
        modalEquipments.innerHTML = '<span class="text-muted fst-italic">Chargement des équipements...</span>';
        
        // Réinitialiser les autres champs
        modalDescription.textContent = '';
        modalType.textContent = '';
        modalSurface.textContent = '';
        modalRooms.textContent = '';
        modalBedrooms.textContent = '';
        modalBathrooms.textContent = '';
        modalFloor.textContent = '';
        modalHasElevator.textContent = '';
        modalHasBalcony.textContent = '';
        modalHasGarage.textContent = '';
        modalHasParking.textContent = '';
        modalTypeOfGarage.textContent = '';
        modalTypeOfParking.textContent = '';
        modalOtherRules.textContent = '';


        try {
            const response = await fetch(`/api/home/${homeId}`);
            if (!response.ok) {
                throw new Error('Erreur réseau ou du serveur.');
            }
            const homeDetails = await response.json();

            // Mettre à jour la modale avec les données de l'API
            modalTime.textContent = homeDetails.timeTravelBetweenHomeAndWorkplace + ' min';
            
            // Mise à jour des nouvelles propriétés
            modalDescription.textContent = homeDetails.description;
            // Pour le type et l'étage, l'API renvoie un objet. On accède à la propriété 'name'.
            modalType.textContent = homeDetails.type.name;
            modalSurface.textContent = homeDetails.surface;
            modalRooms.textContent = homeDetails.rooms;
            modalBedrooms.textContent = homeDetails.bedrooms;
            modalBathrooms.textContent = homeDetails.bathrooms;
            modalFloor.textContent = homeDetails.floor.name;
            // Pour les booléens, on affiche "Oui" ou "Non"
            modalHasElevator.textContent = homeDetails.hasElevator ? 'Oui' : 'Non';
            modalHasBalcony.textContent = homeDetails.hasBalcony ? 'Oui' : 'Non';
            modalHasGarage.textContent = homeDetails.hasGarage ? 'Oui' : 'Non';
            modalHasParking.textContent = homeDetails.hasParking ? 'Oui' : 'Non';

            // Afficher le type de garage uniquement si le logement en a un
            if (homeDetails.hasGarage) {
                modalTypeOfGarage.textContent = homeDetails.typeOfGarage ? homeDetails.typeOfGarage.name : '';
            } else {
                 modalTypeOfGarage.textContent = '';
            }

            // Afficher le type de parking uniquement si le logement en a un
            if (homeDetails.hasParking) {
                modalTypeOfParking.textContent = homeDetails.typeOfParking ? homeDetails.typeOfParking.name : '';
            } else {
                 modalTypeOfParking.textContent = '';
            }

            modalOtherRules.textContent = homeDetails.otherRules;
            if (homeDetails.otherRules === null) {
                 modalOtherRules.textContent = 'Pas de règles supplémentaires renseignées.';
            }

            // Gestion des équipements
            modalEquipments.innerHTML = '';
            if (homeDetails.equipments && homeDetails.equipments.length > 0) {
                homeDetails.equipments.forEach(equipment => {
                    const badge = document.createElement('span');
                    badge.className = 'badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-3 py-2';
                    badge.textContent = equipment.name; 
                    modalEquipments.appendChild(badge);
                });
            } else {
                modalEquipments.innerHTML = '<span class="text-muted fst-italic">Aucun équipement renseigné.</span>';
            }
        } catch (error) {
            console.error('Échec de la récupération des détails du logement:', error);
            modalTime.textContent = 'Erreur';
            modalEquipments.innerHTML = '<span class="text-danger">Impossible de charger les détails.</span>';
        }
    });
});