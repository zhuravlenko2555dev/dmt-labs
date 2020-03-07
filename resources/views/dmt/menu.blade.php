<ul class="nav nav-pills justify-content-center mb-4">
    <li class="nav-item">
        <a class="nav-link {{$data["lab"] == "lab1" ? "active" : ""}}" href="/dmt/lab1">Лабораторна робота №1</a>
    </li>
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Лабораторна робота №2</a>
        <div class="dropdown-menu">
            <a class="dropdown-item {{$data["lab"] == "lab2_savage" ? "active" : ""}}" href="/dmt/lab2_savage">Критерій Севіджа</a>
            <a class="dropdown-item {{$data["lab"] == "lab2_expected_value" ? "active" : ""}}" href="/dmt/lab2_expected_value">Критерій очікуваного значення</a>
            <a class="dropdown-item {{$data["lab"] == "lab2_expected_value_dispersion" ? "active" : ""}}" href="/dmt/lab2_expected_value_dispersion">Критерій очікуваного значення дисперсія</a>
            <a class="dropdown-item {{$data["lab"] == "lab2_average_expected_value" ? "active" : ""}}" href="/dmt/lab2_average_expected_value">Критерій середнього очікуваного значення</a>
            <a class="dropdown-item {{$data["lab"] == "lab2_limit_level" ? "active" : ""}}" href="/dmt/lab2_limit_level">Критерій граничного рівня</a>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link {{$data["lab"] == "lab3" ? "active" : ""}}" href="/dmt/lab3">Лабораторна робота №3</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{$data["lab"] == "lab4" ? "active" : ""}}" href="/dmt/lab4">Лабораторна робота №4</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{$data["lab"] == "lab5" ? "active" : ""}}" href="/dmt/lab5">Лабораторна робота №5</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{$data["lab"] == "lab6" ? "active" : ""}}" href="/dmt/lab6">Лабораторна робота №6</a>
    </li>
</ul>
