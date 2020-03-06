<div class="col-2">
    <form class="mb-4" action={{"/dmt/" . $data["lab"]}} method="post" enctype="multipart/form-data">
        <div class="form-group">
            @csrf
            <label for="excelFile">Виберіть файл excel з даними:</label>
            <input type="file" accept=".xlsx" name="excel" class="form-control-file" id="excelFile" required>
            <input type="submit" value="Загрузити файл" name="submit">
        </div>
    </form>

    <div class="card">
        <h5 class="card-header">Список завантажених файлів:</h5>
        <div class="card-body" style="padding-left: 0; padding-right: 0; padding-bottom: 0">
            @if(empty($data["files"]))
                <p class="card-text" style="margin-left: 1.25rem; margin-right: 1.25rem">Немає завантажених файлів.</p>
            @endif

            <div class="list-group">
                @foreach($data["files"] as $file)
                    <a href="/dmt/{{$data["lab"]}}?file_selected={{$file["uuid"]}}" class="list-group-item list-group-item-action {{$data["file_selected"] === $file["uuid"] ? "active" : ""}}"><i class="fa fa-file-excel-o fa-fw" aria-hidden="true"></i>&nbsp;{{$file["original_name"]}}</a>
                @endforeach
            </div>
        </div>
    </div>
</div>
