<script src="https://code.jquery.com/jquery-3.4.1.min.js"
    integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>

<script>
    let maxLimit = 5000;
    $('#characterCount').css('display', 'none');


    $(document).ready(function () {
        let $characterCount = $('#characterCount');
        let $htmlField = $('#html');


        if($htmlField.val().length > 0 ) {
            showCounter($characterCount, $htmlField.val().length)
        }

        $htmlField.keyup(function () {
           showCounter($characterCount, this.value.length)
        })

        /**
         * Show counter on screen
         * @param $el
         * @param characterLength
         */
        function showCounter($el, characterLength) {
            let characterCount = characterLength;
            let charactersLeft = 0;
            if (characterCount > maxLimit) {
                charactersLeft = maxLimit - characterCount + 1;
            } else {
                charactersLeft = maxLimit - characterCount;
            }

            $el.css('display', 'block');
            $el.text(charactersLeft + ' Characters left');
        }

    })
</script>

@yield('scripts')
