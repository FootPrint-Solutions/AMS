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
    <input type="text" class="form-control autocomplete" data-url="{{ $url }}"
        data-targets="{{ $targets }}" placeholder="{{ $placeholder }}">
    <ul class="list-group autocomplete-list"></ul>
</div>

<script>
    $(document).ready(function() {
        $(".autocomplete").on("input", function() {
            let autocomplete = $(this);
            let autocompleteList = $(".autocomplete-list");

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

                            // Hide current autocomplete item list.
                            autocompleteList.empty();
                        });
                        autocompleteList.append(listItem);
                    });
                }
            });
        });
    });
</script>
