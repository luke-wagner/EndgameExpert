function open_link(link){
    console.log(link);
    window.open(link, '_blank').focus();
}

// Function to inject iframes into the game grid when details are opened
document.querySelectorAll('details').forEach((details, index) => {
    details.addEventListener('toggle', function() {
        if (this.open) {
            let gameGrid = document.getElementById('gameGrid' + index);
            
            if (gameGrid.hasChildNodes()) {
                return; // Don't add more iframes if already loaded
            }

            let descriptor = this.getAttribute('data-descriptor'); // Get the descriptor from the data attribute
            
            var queryString = window.location.search;
            var urlParams = new URLSearchParams(queryString);
            var username = urlParams.get('username')
            var start_date = urlParams.get('start-date')
            var end_date = urlParams.get('end-date')

            var loopCounter = 0;

            fetch('../scripts/script02_games_by_descriptor.php?descriptor=' + descriptor + "&username=" + username + "&start_date="+ start_date + "&end_date=" + end_date)
                .then(response => response.json())  // Parse JSON from PHP response
                .then(data => {
                    console.log(data);

                    data.forEach(game => {
                        if (loopCounter < 18){
                            // Create the descriptive text elements
                            let titleText = document.createElement('p');
                            titleText.textContent = `vs ` + game.opponent + ' (' + game.opp_rating + ')';
                            titleText.style.textAlign = 'center';
                            titleText.style.fontWeight = 'bold';
                            titleText.style.margin = '5px 0';

                            let expectedText = document.createElement('p');
                            expectedText.textContent = `Expected Result: `;
                            if (game.eval == '1'){
                                expectedText.textContent += `Win`;
                            } else if (game.eval == '-1') {
                                expectedText.textContent += `Loss`;
                            } else if (game.eval == '0') {
                                expectedText.textContent += `Draw`;
                            } else {
                                expectedText.textContent += `---`;
                            }
                            expectedText.style.textAlign = 'center';
                            expectedText.style.margin = '2px 0';

                            let actualText = document.createElement('p');
                            actualText.textContent = `Actual Result: `;
                            if (game.outcome == '1'){
                                actualText.textContent += `Win`;
                            } else if (game.outcome == '-1') {
                                actualText.textContent += `Loss`;
                            } else if (game.outcome == '0') {
                                actualText.textContent += `Draw`;
                            }
                            actualText.style.textAlign = 'center';
                            actualText.style.margin = '2px 0';

                            // Create and insert the iframe
                            var iframe = document.createElement('iframe');
                            iframe.src = 'https://mutsuntsai.github.io/fen-tool/gen/?fen=' + game.fen;  // Replace with your iframe source
                            iframe.style.border = 'none';  // Remove default iframe border
                            iframe.style.width = '354px';  // Set width exactly matching container's size
                            iframe.style.height = '354px';  // Set height exactly matching container's size
                            iframe.style.position = 'absolute';  // Position iframe absolutely
                            iframe.style.top = '0px';  // Offset iframe to bring it behind the top border
                            iframe.style.left = '0px';  // Offset iframe to bring it behind the left border
                            //iframe.style.right = '-20px';  // Offset iframe to hide it behind the right border
                            //iframe.style.bottom = '-20px';  // Offset iframe to hide it behind the bottom border

                            // Create and insert the overlay div
                            var overlay = document.createElement('div');
                            overlay.style.position = 'absolute';
                            overlay.style.top = '0';
                            overlay.style.left = '0';
                            overlay.style.width = '100%';
                            overlay.style.height = '100%';
                            overlay.style.cursor = 'pointer';  // Indicates that it's clickable

                            // Attach an event listener to handle clicks
                            overlay.addEventListener('click', function() { open_link(game.game_link); });

                            // Append both elements to a container
                            var container = document.createElement('div');
                            container.style.position = 'relative';  // Make the container relative to position the overlay
                            container.style.width = '354px';  // Set width to match the iframe's visible area
                            container.style.height = '354px';  // Set height to match the iframe's visible area

                            // Add border and rounded corners
                            //container.style.border = '6px solid #2EB432';  // Set border color and thickness
                            container.style.borderRadius = '18px';  // Rounded corners
                            container.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.1)';  // Subtle shadow for a lifted effect
                            container.style.overflow = 'hidden';  // Ensure iframe stays within the rounded border
                            container.style.top = '-4px';
                            container.style.left = '-4px';
                            container.style.zIndex = '1';

                            var container2 = document.createElement('div');
                            container2.style.width = '345px';  // Set width to match the iframe's visible area
                            container2.style.height = '345px';  // Set height to match the iframe's visible area
                            console.log(game.outcome);
                            if (game.outcome == '1'){
                                container2.style.border = '6px solid #2EB432';  // Set border color and thickness
                            } else if (game.outcome == '-1') {
                                container2.style.border = '6px solid #F54133';  // Set border color and thickness
                            } else if (game.outcome == '0') {
                                container2.style.border = '6px solid #808080';  // Set border color and thickness
                            }
                            container2.style.borderRadius = '18px';  // Rounded corners
                            container2.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.1)';  // Subtle shadow for a lifted effect
                            container2.style.overflow = 'hidden';  // Ensure iframe stays within the rounded border
                            container2.style.zIndex = '2';

                            // Append elements to the container
                            container.appendChild(iframe);
                            container.appendChild(overlay);

                            container2.appendChild(container);

                            // Create a wrapper for the descriptive text and iframe
                            let wrapper = document.createElement('div');
                            wrapper.style.textAlign = 'center';  // Center align text and iframe
                            wrapper.appendChild(titleText);
                            wrapper.appendChild(expectedText);
                            wrapper.appendChild(actualText);
                            wrapper.appendChild(container2);

                            gameGrid.appendChild(wrapper);
                        }

                        loopCounter += 1;

                        //console.log(game.game_link);
                        // Create new elements for each entry
                        //const gameContainer = document.createElement('div');
                        //gameContainer.classList.add('game-item');

                        //const fenText = document.createElement('p');
                        //fenText.textContent = `FEN: ${game.fen}`;

                        //const link = document.createElement('a');
                        //link.href = game.game_link;
                        //link.textContent = "View Game";

                        // Append elements to the container
                        //gameContainer.appendChild(fenText);
                        //gameContainer.appendChild(link);

                        // Append container to the DOM (adjust selector as needed)
                        //document.getElementById('games-list').appendChild(gameContainer);
                    });
                })
                .catch(error => {
                    console.error('Error fetching games:', error);
                });
                
            /*
            if (!gameGrid.hasChildNodes()) { // Only load if not already loaded
                for (let i = 0; i < 6; i++) {
                    let iframe = document.createElement('iframe');
                    var testFen = '2K5/P7/3kN2p/3n3P/8/8/8/8'
                    iframe.src = 'https://mutsuntsai.github.io/fen-tool/gen/?fen=' + testFen;
                    iframe.style.border = 'none';
                    iframe.style.width = '354px';
                    iframe.style.height = '354px';
                    gameGrid.appendChild(iframe);
                }
                console.log(`Games loaded for descriptor: ${descriptor}`); // Use descriptor for tracking
            }
            */
        }
    });
});