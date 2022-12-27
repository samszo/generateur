export class sql {
    constructor(params) {
        var me = this,
        w = new Worker("asset/js/worker.sql-wasm.js");
        this.init = function () {

            // Open a database
            w.postMessage({ action: 'open' });
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'bdd/generateur.db', true);
            xhr.responseType = 'arraybuffer';
            
            xhr.onload = e => {
                const uInt8Array = new Uint8Array(xhr.response);
                w.onmessage = function () {
                    // Show the schema of the loaded database
                    execute("SELECT `name`, `sql`\n  FROM `sqlite_master`\n  WHERE type='table';");
                };
        
                try {
                    w.postMessage({ action: 'open', buffer: uInt8Array}, [uInt8Array]);
                }
                catch (exception) {
                    w.postMessage({ action: 'open', buffer: uInt8Array});
                }
            };
            xhr.send();                              
        }
        // Run a command in the database
        function execute(commands) {
            w.onmessage = function (event) {
                var results = event.data.results;
                if (!results) {
                    console.log(event.data.error);
                    return;
                }
                console.log(results);
                return results;
            }
            w.postMessage({ action: 'exec', sql: commands });
        }



        this.init();
    
    }
}
