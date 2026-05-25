<style>
    .autocomplete-list {
        position: absolute;
        z-index: 1000;
        width: 100%;
    }

    .list-item {
        cursor: pointer;
    }

    .list-item:hover {
        background-color: #f5f5f5;
    }
</style>

<div style="position: relative;">
    <input type="hidden" class="hidden-id-input" name={{ $nameHiddenId }} value="{{ $valueHiddenId }}">
    <input type="text" class="form-control autocomplete {{ $class }}" id={{ $id }}
        name={{ $name }} data-url="{{ $url }}" data-targets="{{ $targets }}" required
        placeholder="{{ $placeholder }}" @if ($value !== '')
value="{{ $value }}" @endempty>
<ul class="list-group autocomplete-list"></ul>
</div>

<script>
    $(document).on("input", ".autocomplete", function() {
        let autocomplete = $(this);
        let autocompleteList = autocomplete.closest("div").find(".autocomplete-list");
        let idInput = autocomplete.closest("div").find(".hidden-id-input");

        // Clear current autocomplete item list.
        autocompleteList.empty();

        // Get autocomplete data and append autocomplete item list.
        $.ajax({
            url: $(this).data("url") + $(this).val(),
            method: "GET",
            success: function(response) {
                response.forEach(function(item) {
                    let data = Object.values(item);

                    var listItem = $(
                        "<li class='list-group-item list-item' data-id='" +
                        data[0] + "'>" + data[1] + "</li>");

                    // Add onclick event function.
                    listItem.click(function() {
                        // Set target input values based on target.
                        let targets = autocomplete.data("targets");
                        var index = 2;
                        targets.forEach(function(target) {
                            $("#" + target).val(data[index]);
                            index++;
                        });

                        // Set input value.
                        autocomplete.val(data[1]);
                        idInput.val(data[0]);

                        // Hide current autocomplete item list.
                        autocompleteList.empty();

                        // Do a function after the autocomplete process.
                        {{ $callback }}();
                    });
                    autocompleteList.append(listItem);
                });
            }
        });
    });
</script>
