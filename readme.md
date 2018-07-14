This is a small Plugin for jQuery that creates an TimeInput widget to set up a 24hour (configurable) time control for home control tasks. For example for heating times, lighting or roller shutters.

The example code provides an simple ajax handler that stores the times in an database.

Example Code:

        $('.YourSelector').hctimeprogram({
            selstop: function( event, data ) {
                let hcdata = $(this).hctimeprogram("getarray");
                let id = <?php echo $shutter->getId();?>;
                $.ajax({
                    method: "POST",
                    url: "ajax.php",
                    data: { id: id, data: hcdata }
                })
                    .done(function( msg ) {
                        // Do something after saving the Data
                        // or check if the return value confirms a successfull save
                        //alert( "Data Saved: " + msg );
                    });
            }
        });
`

You can find a demo at: https://www.jh-caspers.de/hctimeinput/

The MIT License (MIT)

Copyright (c) 2018 Jan-Hendrik Caspers

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 