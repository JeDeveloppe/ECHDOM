document.addEventListener('DOMContentLoaded', () => {
    console.log('Script de géocodage lancé. En attente de l\'ouverture de la modale.');

    const form = document.getElementById('workplaceForm');
    const messageDiv = document.getElementById('workplaceMessage');
    const workplaceAddressParagraph = document.getElementById('workplaceAddress');
    const buttonWhoOpenWorkplaceModal = document.getElementById('buttonWhoOpenWorkplaceModal');

    buttonWhoOpenWorkplaceModal.addEventListener('click', () => {
        console.log("Bouton d'ouverture de la modale cliqué.");
        const modalElement = document.getElementById('addWorkplaceModal');

        let modal = null;
    

                modal = new bootstrap.Modal(modalElement);
    
                console.log("Événement 'shown.bs.modal' détecté. La modale est maintenant ouverte.");
                
                const addressInput = document.getElementById('user_workplace_choice_address');
                const suggestionsContainer = document.getElementById('address-suggestions');
                const latitudeInput = document.getElementById('user_workplace_choice_latitude');
                const longitudeInput = document.getElementById('user_workplace_choice_longitude');
    
                console.log("Élément 'addressInput' trouvé:", !!addressInput);
    
                // Fonction pour gérer la soumission du formulaire
                async function handleFormSubmission(event) {
                    event.preventDefault();
                    console.log('Soumission du formulaire déclenchée.');
    
                    messageDiv.textContent = '';
                    messageDiv.classList.add('d-none');
    
                    const formData = new FormData(form);
                    const data = {};
                    formData.forEach((value, key) => {
                        data[key] = value;
                    });
    
                    const url = '/member/api/workplace/update';
    
                    console.log('--- Début de la soumission du formulaire ---');
                    console.log('URL de la requête :', url);
                    console.log('Données du formulaire :', data);
    
                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(data),
                        });
                        
                        console.log('Réponse reçue du serveur :', response);
                        
                        if (!response.ok) {
                            const errorResult = await response.json();
                            throw new Error(errorResult.error || 'Une erreur est survenue lors de la mise à jour.');
                        }
                        
                        const result = await response.json();
                        
                        console.log('Résultat de la requête :', result);
    
                        messageDiv.className = 'mt-3 text-center text-success';
                        messageDiv.textContent = 'Adresse de travail mise à jour avec succès!';
                        workplaceAddressParagraph.textContent = result.address;
                        
                        setTimeout(() => {
                            modal.hide();
                        }, 1500);
    
                    } catch (error) {
                        messageDiv.className = 'mt-3 text-center text-danger';
                        messageDiv.textContent = `Erreur: ${error.message}`;
                        console.error('Erreur de la requête fetch :', error);
                    }
                    console.log('--- Fin de la soumission du formulaire ---');
                }
                
                // Écoute l'événement de soumission du formulaire
                if (form) {
                    form.removeEventListener('submit', handleFormSubmission);
                    form.addEventListener('submit', handleFormSubmission);
                } else {
                    console.error('Erreur: Formulaire introuvable avec l\'ID workplaceForm.');
                }
                
                // --- Logique d'autocomplétion ---
                let timeout = null;
                
                if (addressInput) {
                    console.log("Écoute de l'événement 'input' sur le champ d'adresse.");
                    const onInputHandler = function() {
                        console.log("Événement 'input' détecté.");
                        clearTimeout(timeout);
                        const query = this.value.trim();
    
                        console.log("Texte saisi :", query);
    
                        if (query.length < 3) {
                            console.log("Requête trop courte, effacement des suggestions.");
                            suggestionsContainer.innerHTML = '';
                            return;
                        }
    
                        timeout = setTimeout(async () => {
                            console.log("Délai de recherche terminé, envoi de la requête à l'API.");
                            try {
                                const response = await fetch(`/member/api/geocode/search?q=${encodeURIComponent(query)}`);
                                const result = await response.json();
    
                                console.log('Résultat de l\'API:', result);
                                
                                suggestionsContainer.innerHTML = '';
                                if (result.address && result.y && result.x) {
                                    console.log('Résultat valide trouvé:', result.address);
                                    const suggestionItem = document.createElement('a');
                                    suggestionItem.href = '#';
                                    suggestionItem.className = 'list-group-item list-group-item-action';
                                    suggestionItem.textContent = result.address;
                                    suggestionItem.dataset.lat = result.y;
                                    suggestionItem.dataset.lon = result.x;
                                    
                                    suggestionItem.addEventListener('click', function(e) {
                                        e.preventDefault();
                                        addressInput.value = this.textContent;
                                        latitudeInput.value = this.dataset.lat;
                                        longitudeInput.value = this.dataset.lon;
                                        suggestionsContainer.innerHTML = '';
                                    });
                                    
                                    suggestionsContainer.appendChild(suggestionItem);
                                } else {
                                    console.log('Aucun résultat valide trouvé.');
                                    const noResultsItem = document.createElement('div');
                                    noResultsItem.className = 'list-group-item text-muted fst-italic';
                                    noResultsItem.textContent = 'Aucun résultat trouvé.';
                                    suggestionsContainer.appendChild(noResultsItem);
                                }
                            } catch (error) {
                                console.error('Erreur de l\'autocomplétion :', error);
                            }
                        }, 300);
                    }
                    
                    addressInput.removeEventListener('input', onInputHandler);
                    addressInput.addEventListener('input', onInputHandler);
    
                } else {
                    console.error("Erreur: Le champ d'adresse n'a pas été trouvé.");
                }
   
    
            document.addEventListener('click', function(event) {
                if (!event.target.closest('#addWorkplaceModal')) {
                    const suggestionsContainer = document.getElementById('address-suggestions');
                    if (suggestionsContainer) {
                        suggestionsContainer.innerHTML = '';
                    }
                }
            });

    });

});
