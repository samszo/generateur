import {modal} from './modal.js';

export class writeGen {
    constructor(params) {
        var me = this;
        this.oeuvre = params.oeuvre ? params.oeuvre : false;
        this.omk = params.omk ? params.omk : false;
        this.apiUrl = me.omk.api.replace("api","s/balpien/page/ajax")+"?json=1&helper=sql&action=getConceptSuggest&idOeu="+me.oeuvre.curOeuvre["o:id"]+"&query=";
        this.isActive = false;
        this.textarea = params.textarea ? params.textarea : false;
        this.textarea = params.textarea ? params.textarea : false;
        this.buffer= '',
        this.suggestions= [],
        this.startChar= '[',
        this.endChar= ']',
        this.separator= '|';        
        var m=new modal(),suggestionsContainer;

        this.init = function () {
            me.textarea.on('input',handleInput);
            me.textarea.on('keydown', function(e) {
                if (e.key === 'Escape' && me.isActive) {
                    me.isActive = false;
                    me.buffer = '';
                    me.suggestions = [];
                    e.target.value += me.endChar; // Ajoute le caractère de fin
                    e.target.dispatchEvent(new Event('input')); // Déclenche l'événement input pour mettre à jour les suggestions
                }
            });
            me.textarea.on('blur', function() {
                if (me.isActive) {
                    me.isActive = false;
                    me.buffer = '';
                    me.suggestions = [];
                }
            });
            me.textarea.on('blur', function() {
                if (me.isActive) {
                    me.isActive = false;
                    me.buffer = '';
                    me.suggestions = [];
                }
            });
            // Ajoute un conteneur pour les suggestions
            suggestionsContainer = document.createElement('div');
            suggestionsContainer.id = 'suggestions-container';
            suggestionsContainer.style.position = 'absolute';
            suggestionsContainer.style.zIndex = '1000';
            suggestionsContainer.style.display = 'none';
            const parent = me.textarea.node().parentNode;
            parent.appendChild(suggestionsContainer);
            // Gère le clic en dehors du conteneur de suggestions
            parent.addEventListener('click', function(event) {
                suggestionsContainer = d3.select('#suggestions-container').node();
                if (suggestionsContainer && !suggestionsContainer.contains(event.target) && !me.textarea.node().contains(event.target)) {
                    me.isActive = false;
                    me.buffer = '';
                    me.suggestions = [];
                    suggestionsContainer.style.display = 'none';
                }
            }); 
        }

        /**
         * Gère l'entrée utilisateur dans le textarea.
         * @param {InputEvent} e
         */
        function handleInput(e) {
            const textarea = e.target;
            const value = textarea.value;
            const cursorPos = textarea.selectionStart;
            const char = value[cursorPos - 1];

            if (char === me.startChar && !me.isActive) {
                me.isActive = true;
                me.buffer = '';
                me.suggestions = [];
            } else if (char === me.endChar) {
                me.isActive = false;
                me.buffer = '';
                me.suggestions = [];
            } else if (me.isActive) {
                if (char === me.separator) {
                    me.buffer = '';
                    me.suggestions = [];
                //} else if (/[a-zA-Z0-9]/.test(char)) {
                } else {
                    me.buffer += char;
                    //lance la requête à trois caractères
                    if (me.buffer.length >= 3) {
                        fetchSuggestions(me.buffer).then(suggestions => {
                            me.suggestions = suggestions;
                            showSuggestions(textarea, suggestions);
                        });
                    }
                }
            }
        }

        /**
         * Appelle l'API OMK pour obtenir des suggestions.
         * @param {string} query
         * @returns {Promise<string[]>}
         */
        async function fetchSuggestions(query) {
            if (!query) return [];
            try {
                console.log(`Fetching suggestions for query: ${query}`);
                const response = await fetch(`${me.apiUrl}${query}`);//${encodeURIComponent(query)}`);
                return await response.json();
            } catch (err) {
                return [];
            }
        }

        /**
         * Affiche les suggestions (à adapter selon votre UI).
         * @param {HTMLTextAreaElement} textarea
         * @param {string[]} suggestions
         */
        function showSuggestions(textarea, suggestions) {
            // À implémenter selon votre interface utilisateur
            console.log(suggestions);
            d3.select('#suggestions-container').select('#suggestions-dropdown').remove();
            const rect = textarea.getBoundingClientRect();
            let dropdown = d3.select('#suggestions-container').append('ul')
                .attr('id', 'suggestions-dropdown')
                .style('position', 'absolute')
                .style('background', '#fff')
                .style('color', 'black')
                .style('border', '1px solid #ccc')
                .style('overflow-y', 'scroll')
                .style('width', rect.width+'px')
                .style('height', '300px')
                .style('left', '56px')
                .style('top', '100px')
                .style('cursor', 'pointer')
                .style('text-align', 'left');
            suggestionsContainer.style.display = 'block';
            if (suggestions.length > 0) {
                dropdown.selectAll('li').data(suggestions).enter().append('li')
                    .html(s=> s.title.replace(me.buffer,"<span style='color:#27b638ff;'>"+me.buffer+"</span>")+" ("+s.label+")")
                    //.style('padding', '0px')
                    .style('border-bottom', '1px solid #ccc')
                    .style('cursor', 'pointer')
                    .on('mouseover', (e,d)=> {
                        d3.select(e.currentTarget).style('background-color', '#e01b1bff');
                    })
                    .on('mouseout', (e,d)=> {
                        d3.select(e.currentTarget).style('background-color', '#c3ccd7ff');
                    })
                    .on('mousedown', function(e,s) {
                        e.preventDefault();
                        let value = textarea.value, 
                            cursorPos = textarea.selectionStart,
                            startIdx = 0,
                            endIdx = 0,
                            gen = s.idDet ? me.startChar+s.idDet : s.idSyn ? me.startChar+"#"+s.idSyn+me.endChar : me.startChar+s.title;
                        //retrouver le début du générateur ou le séparateur
                        for (let index = value.length; index > 0; index--) {
                            if(value[index]=== me.startChar || value[index] === me.separator) {
                                startIdx = index;
                                break;
                            }                            
                        }
                        //const startIdx = value.lastIndexOf(me.separator, cursorPos - 1) + 1;
                        //const endIdx = value.lastIndexOf(me.separator, cursorPos - 1) + 1;
                        textarea.value = value.substring(0,startIdx) + gen + value.substring(cursorPos);
                        textarea.selectionStart = textarea.selectionEnd = startIdx + s.title.length + 1; // +1 pour le séparateur
                        //me.isActive = false;
                        me.buffer = '';
                        me.suggestions = [];
                        dropdown.style("display",'none');
                });
            } else {
                dropdown.innerHTML = '';
            }
        }

        this.init();
    }
}
