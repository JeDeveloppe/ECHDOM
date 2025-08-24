document.addEventListener('DOMContentLoaded', () => {
            // S'assurer que les éléments sont bien chargés avant de les sélectionner
            const form = document.getElementById('workplaceForm');
            // Mise à jour des sélecteurs pour correspondre au nouveau nom de formulaire
            const addressInput = document.getElementById('full_address_address');
            const latitudeInput = document.getElementById('full_address_latitude');
            const longitudeInput = document.getElementById('full_address_longitude');
            const csrfInput = document.getElementById('full_address__token');
            const addressDisplay = document.getElementById('workplaceAddress');
            const messageContainer = document.getElementById('workplace-message');
            const resultsContainer = document.getElementById('autocomplete-results');
            const saveButton = document.getElementById('saveButton');
            const buttonText = document.getElementById('buttonText');
            const saveIcon = document.getElementById('saveIcon');
            const loadingIcon = document.getElementById('loadingIcon');

            // Effacer le champ d'entrée au chargement de la page pour afficher le placeholder
            addressInput.value = '';

            // Vérifier si les éléments existent avant de continuer
            if (!form || !addressInput) {
                console.error("Erreur : Le formulaire ou le champ d'adresse n'a pas été trouvé.");
                return;
            }

            let debounceTimeout;

            // Fonction pour gérer la recherche d'adresse
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
                if (!resultsContainer) return;

                resultsContainer.innerHTML = '';
                if (results.length > 0) {
                    resultsContainer.style.display = 'block';
                    results.forEach(result => {
                        const suggestion = document.createElement('div');
                        suggestion.classList.add('p-2', 'text-start', 'cursor-pointer', 'text-dark');
                        suggestion.textContent = result.label;
                        
                        // Stocker les données directement sur l'élément de suggestion
                        suggestion.dataset.latitude = result.latitude;
                        suggestion.dataset.longitude = result.longitude;

                        // Ajoute un écouteur d'événement pour le clic sur une suggestion
                        suggestion.addEventListener('click', (e) => {
                            e.stopPropagation();

                            addressInput.value = result.label;
                            // Mettre à jour la valeur des champs cachés
                            latitudeInput.value = suggestion.dataset.latitude;
                            longitudeInput.value = suggestion.dataset.longitude;
                            
                            resultsContainer.style.display = 'none';
                            toggleSaveButton(); // Appelle la fonction pour afficher le bouton
                        });

                        // Ajoute les classes de style au survol
                        suggestion.addEventListener('mouseenter', () => suggestion.classList.add('bg-light'));
                        suggestion.addEventListener('mouseleave', () => suggestion.classList.remove('bg-light'));

                        resultsContainer.appendChild(suggestion);
                    });
                } else {
                    resultsContainer.style.display = 'none';
                }
            };

            // Fonction pour afficher ou cacher le bouton de sauvegarde
            const toggleSaveButton = () => {
                if (latitudeInput.value && longitudeInput.value) {
                    saveButton.style.display = 'block';
                } else {
                    saveButton.style.display = 'none';
                }
            };

            // Écouteur d'événement pour l'entrée utilisateur (avec debounce)
            addressInput.addEventListener('input', () => {
                clearTimeout(debounceTimeout);
                // Efface les champs latitude/longitude dès que l'utilisateur commence à taper
                latitudeInput.value = '';
                longitudeInput.value = '';
                toggleSaveButton(); // Cache le bouton dès que l'utilisateur commence à taper
                debounceTimeout = setTimeout(() => {
                    searchAddress(addressInput.value);
                }, 300);
            });
            
            // Masquer les résultats lorsque l'utilisateur clique en dehors
            document.addEventListener('click', (e) => {
                if (!resultsContainer.contains(e.target) && e.target !== addressInput) {
                    resultsContainer.style.display = 'none';
                }
            });
            
            // Gère la soumission du formulaire
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                messageContainer.innerHTML = '';

                if (!latitudeInput.value || !longitudeInput.value) {
                    messageContainer.innerHTML = `
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            Veuillez sélectionner une adresse dans la liste de suggestions.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                    return;
                }
                
                showLoadingState();

                try {
                    const response = await fetch('/member/api/workplace/update', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            address: addressInput.value,
                            latitude: latitudeInput.value,
                            longitude: longitudeInput.value,
                            _token: csrfInput.value // Ajout du jeton CSRF
                        })
                    });

                    const result = await response.json();

                    if (response.ok) {
                        addressDisplay.textContent = result.address;
                        messageContainer.innerHTML = `
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                Adresse mise à jour avec succès !
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `;
                        // Efface l'input, la latitude et la longitude pour cacher le bouton
                        addressInput.value = '';
                        latitudeInput.value = '';
                        longitudeInput.value = '';
                        toggleSaveButton();

                    } else {
                        messageContainer.innerHTML = `
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                Erreur : ${result.error || 'Une erreur inconnue est survenue.'}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `;
                        console.error('Erreur de soumission:', result.error);
                    }
                } catch (error) {
                    messageContainer.innerHTML = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            Erreur réseau. Veuillez réessayer.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                    console.error('Erreur réseau ou du serveur:', error);
                } finally {
                    hideLoadingState();
                }
            });

            // Fonctions pour gérer l'état de chargement du bouton
            const showLoadingState = () => {
                saveButton.disabled = true;
                saveIcon.style.display = 'none';
                loadingIcon.style.display = 'inline-block';
                buttonText.textContent = 'Enregistrement...';
            };

            const hideLoadingState = () => {
                saveButton.disabled = false;
                saveIcon.style.display = 'inline-block';
                loadingIcon.style.display = 'none';
                buttonText.textContent = 'Enregistrer';
            };
        });