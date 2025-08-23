 function updateDurationValue(val) {
            document.getElementById('durationValue').textContent = val;
        }
        function updateDistanceValue(val) {
            document.getElementById('distanceValue').textContent = val;
        }

        // Fonction pour afficher les équipements dans la modal
        function showHomeDetails(address, equipments, price, timeTravel) {
            let modalBody = document.getElementById('modalEquipmentsBody');
            let html = '<div class="mb-2"><strong>Adresse :</strong> ' + address + '</div>';
            html += '<div class="mb-2"><strong>Prix :</strong> ' + (price ? price + ' €' : 'N/A') + '</div>';
            html += '<div class="mb-2"><strong>Temps de trajet :</strong> ' + timeTravel + ' min</div>';
            html += '<hr class="my-2">';
            if (equipments && equipments.length > 0) {
                html += '<ul class="list-group mb-2">';
                equipments.forEach(function(equipment) {
                    html += '<li class="list-group-item">' + (equipment.name ? equipment.name : 'Équipement inconnu') + '</li>';
                });
                html += '</ul>';
            } else {
                html += '<div class="alert alert-warning rounded-pill">Aucun équipement pour ce logement.</div>';
            }
            modalBody.innerHTML = html;

            let modal = new bootstrap.Modal(document.getElementById('equipmentsModal'));
            modal.show();
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Range initial values
            var durationInput = document.getElementById('form_duration');
            var durationValue = document.getElementById('durationValue');
            if(durationInput && durationValue) {
                durationValue.textContent = durationInput.value;
                durationInput.addEventListener('input', function() {
                    durationValue.textContent = durationInput.value;
                });
            }
            var distanceInput = document.getElementById('form_distance');
            var distanceValue = document.getElementById('distanceValue');
            if(distanceInput && distanceValue) {
                distanceValue.textContent = distanceInput.value;
                distanceInput.addEventListener('input', function() {
                    distanceValue.textContent = distanceInput.value;
                });
            }

            // Loader on submit
            const submitButton = document.getElementById('submitButton');
            const loaderOverlay = document.getElementById('loader-overlay');
            if (submitButton && loaderOverlay) {
                submitButton.addEventListener('click', function () {
                    loaderOverlay.style.display = 'flex';
                    setTimeout(function () {
                        loaderOverlay.style.display = 'none';
                    }, 1500);
                });
            }
        });