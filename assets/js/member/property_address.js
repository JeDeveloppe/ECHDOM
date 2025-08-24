document.addEventListener('DOMContentLoaded', () => {
    // SÉLECTION DES ÉLÉMENTS DU FORMULAIRE
    const form = document.getElementById('propertyForm');
    const addressInput = document.getElementById('home_address');
    const latitudeInput = document.getElementById('home_latitude');
    const longitudeInput = document.getElementById('home_longitude');
    const messageContainer = document.getElementById('property-message');
    const resultsContainer = document.getElementById('autocomplete-results');
    const submitButton = form.querySelector('button[type="submit"]');

    if (!form || !addressInput || !submitButton || !messageContainer || !resultsContainer) {
        console.error("Erreur : Un ou plusieurs éléments requis du formulaire n'a pas été trouvé.");
        return;
    }

    let debounceTimeout;

    // Fonction pour gérer la recherche d'adresse (utilisée uniquement pour l'autocomplétion)
    const searchAddress = async (query) => {
        if (query.length < 3) {
            resultsContainer.style.display = 'none';
            return;
        }
        try {
            const response = await fetch('/member/api/geocode?q=' + encodeURIComponent(query));
            if (!response.ok) {
                throw new Error('Erreur de l\'API de géocodage');
            }
            const data = await response.json();
            displayResults(data);
        } catch (error) {
            console.error('Erreur lors de la recherche d\'adresse:', error);
            resultsContainer.style.display = 'none';
        }
    };

    // Fonction pour afficher les résultats de l'autocomplétion
    const displayResults = (results) => {
        resultsContainer.innerHTML = '';
        if (results.length > 0) {
            resultsContainer.style.display = 'block';
            results.forEach(result => {
                const suggestion = document.createElement('div');
                suggestion.classList.add('p-2', 'text-start', 'cursor-pointer', 'text-dark', 'bg-light-hover');
                suggestion.textContent = result.label;
                
                suggestion.dataset.latitude = result.latitude;
                suggestion.dataset.longitude = result.longitude;

                suggestion.addEventListener('click', (e) => {
                    e.stopPropagation();
                    // Les champs latitude et longitude sont remplis ici,
                    // sans faire de nouvel appel API.
                    addressInput.value = result.label;
                    latitudeInput.value = suggestion.dataset.latitude;
                    longitudeInput.value = suggestion.dataset.longitude;
                    
                    resultsContainer.style.display = 'none';
                });

                resultsContainer.appendChild(suggestion);
            });
        } else {
            resultsContainer.style.display = 'none';
        }
    };

    // Écouteur d'événement pour l'entrée utilisateur
    addressInput.addEventListener('input', () => {
        clearTimeout(debounceTimeout);
        latitudeInput.value = '';
        longitudeInput.value = '';
        
        debounceTimeout = setTimeout(() => {
            searchAddress(addressInput.value);
        }, 300);
    });
    
    document.addEventListener('click', (e) => {
        if (!resultsContainer.contains(e.target) && e.target !== addressInput) {
            resultsContainer.style.display = 'none';
        }
    });

    // Gère la soumission du formulaire
    // form.addEventListener('submit', async (e) => {
    //     e.preventDefault();
    //     messageContainer.innerHTML = '';

    //     // VÉRIFIE SI LES CHAMPS SONT DÉJÀ REMPLIS PAR L'AUTOCOMPLÉTION.
    //     // Aucune API de géocodage n'est appelée ici.
    //     if (!latitudeInput.value || !longitudeInput.value) {
    //         messageContainer.innerHTML = `
    //             <div class="alert alert-warning alert-dismissible fade show" role="alert">
    //                 Veuillez sélectionner une adresse dans la liste de suggestions.
    //                 <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    //             </div>
    //         `;
    //         return;
    //     }
        
    //     showLoadingState();

    //     const formData = new FormData(form);
    //     const data = {};
    //     formData.forEach((value, key) => {
    //         if (value === 'on') {
    //             data[key] = true;
    //         } else if (value === '') {
    //             data[key] = '';
    //         } else {
    //             data[key] = value;
    //         }
    //     });

    //     const equipments = [];
    //     const checkedEquipments = form.querySelectorAll('input[type="checkbox"][name^="propertyForm[equipments]"]:checked');
    //     checkedEquipments.forEach(checkbox => {
    //         const name = checkbox.name;
    //         const index = name.match(/\[(\d+)\]/)[1];
    //         equipments.push(checkbox.value);
    //     });
    //     data['propertyForm[equipments]'] = equipments;

    //     try {
    //         const response = await fetch(form.action, {
    //             method: form.method,
    //             headers: {
    //                 'Content-Type': 'application/json',
    //             },
    //             body: JSON.stringify(data)
    //         });

    //         let result;
    //         try {
    //             result = await response.json();
    //         } catch (jsonError) {
    //             const errorText = await response.text();
    //             console.error("Erreur de format de réponse. Réponse du serveur :", errorText);
    //             result = { error: "Erreur serveur. La réponse n'est pas un format valide." };
    //         }

    //         if (response.ok) {
    //             messageContainer.innerHTML = `
    //                 <div class="alert alert-success alert-dismissible fade show" role="alert">
    //                     Bien mis à jour avec succès !
    //                     <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    //                 </div>
    //             `;
    //         } else {
    //             messageContainer.innerHTML = `
    //                 <div class="alert alert-danger alert-dismissible fade show" role="alert">
    //                     Erreur : ${result.error || 'Une erreur inconnue est survenue.'}
    //                     <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    //                 </div>
    //             `;
    //             console.error('Erreur de soumission:', result.error);
    //         }
    //     } catch (error) {
    //         messageContainer.innerHTML = `
    //             <div class="alert alert-danger alert-dismissible fade show" role="alert">
    //                 Erreur réseau. Veuillez réessayer.
    //                 <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    //             </div>
    //         `;
    //         console.error('Erreur réseau ou du serveur:', error);
    //     } finally {
    //         hideLoadingState();
    //     }
    // });

    const showLoadingState = () => {
        submitButton.disabled = true;
        const saveIcon = submitButton.querySelector('#saveIcon');
        const loadingIcon = submitButton.querySelector('#loadingIcon');
        const buttonText = submitButton.querySelector('#buttonText');

        if (saveIcon) saveIcon.style.display = 'none';
        if (loadingIcon) loadingIcon.style.display = 'inline-block';
        if (buttonText) buttonText.textContent = 'Enregistrement...';
    };

    const hideLoadingState = () => {
        submitButton.disabled = false;
        const saveIcon = submitButton.querySelector('#saveIcon');
        const loadingIcon = submitButton.querySelector('#loadingIcon');
        const buttonText = submitButton.querySelector('#buttonText');
        const originalText = submitButton.dataset.originalText;

        if (saveIcon) saveIcon.style.display = 'inline-block';
        if (loadingIcon) loadingIcon.style.display = 'none';
        if (buttonText && originalText) buttonText.textContent = originalText;
    };
});
