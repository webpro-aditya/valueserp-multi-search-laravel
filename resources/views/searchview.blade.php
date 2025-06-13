<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ValueSERP Multi Search (AJAX)</title>
    <link href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
</head>
<body>
    <h1>ValueSERP Multi Search</h1>

    <form id="searchForm">
        @csrf
        @for ($i = 0; $i < 5; $i++)
            <input type="text" name="queries[{{ $i }}]" placeholder="Enter Query {{ $i+1 }}">
            <br>
        @endfor
        <button type="submit">Search</button>
        <button type="button" id="exportAllBtn" style="margin-top: 10px; display:none;">Download All Data (CSV)</button>
    </form>

    <div class="spinner-backdrop"></div>
    <div class="spinner"></div>
    <div id="results"></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.js"></script>
    <script>
        $('#searchForm').on('submit', function(e) {
            e.preventDefault();
            $('.spinner').show();
            $('.spinner-backdrop').show();

            $.ajax({
                url: "{{ route('search') }}",
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    $('.spinner').hide();
                    $('.spinner-backdrop').hide();
                    $('#exportAllBtn').show();
                    toastr.success('Results loaded successfully!');
                    renderTable(response.data);
                },
                error: function() {
                    $('.spinner').hide();
                    toastr.error('No results found or an error occurred.');
                    $('#results').html('');
                }
            });
        });

        function renderTable(data) {
            if (data.length === 0) {
                $('#results').html('<p>No results to display.</p>');
                return;
            }
            let html = '<table><tr><th>Query</th><th>Title</th><th>Link</th><th>Snippet</th></tr>';
            data.forEach(function(row) {
                html += `<tr>
                            <td>${row.query}</td>
                            <td>${row.title}</td>
                            <td><a href="${row.link}" target="_blank">${row.link}</a></td>
                            <td>${row.snippet}</td>
                         </tr>`;
            });
            html += '</table>';
            $('#results').html(html);
        }

        // Export CSV - all data at once
        $('#exportAllBtn').on('click', function() {
            $('.spinner').show();
            $('.spinner-backdrop').show();
            $.ajax({
                url: "{{ route('export') }}",
                method: 'POST',
                data: $('#searchForm').serialize(),
                xhrFields: { responseType: 'blob' },
                success: function(blob) {
                    $('.spinner').hide();
                    $('.spinner-backdrop').hide();
                    toastr.success('File ready for download!');

                    let link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = "search_results.csv";
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                },
                error: function() {
                    $('.spinner').hide();
                    toastr.error('Error exporting data.');
                }
            });
        });
    </script>
</body>
</html>
