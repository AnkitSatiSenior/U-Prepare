<x-guest-layout>
    @section('page_title', 'Home')
    {{-- ================= HERO SLIDER ================= --}}
    <section class="container-fluid slider p-0">
        <div class="hero-slider">
            @foreach (getSlides() as $slide)
                <div>
                    <div class="slider-item"
                        style="background-image: url({{ asset('storage/app/public/' . $slide->img) }})">
                        <div class="overlay"></div>
                        <div class="caption">
                            @if ($slide->head)
                                <h2>{{ $slide->head }}</h2>
                            @endif
                            @if ($slide->subh)
                                <p class="mb-3">{{ $slide->subh }}</p>
                            @endif
                            @if ($slide->link)
                                <a href="{{ $slide->link }}" class="btn">
                                    {{ $slide->btn_text }}
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ================= LEADERS ================= --}}
    <section class="about-sec pt-4">
        <div class="container-fluid p-0">
            <div class="bg-bar">
                <div class="overlay"></div>
                <img src="{{ asset('assets/img/honper-bgi.webp') }}" />
            </div>

            <div class="container-fluid honpers">
                <div class="row">
                    @foreach (getLeaders() as $person)
                        <div class="col-md-3 d-flex flex-column center honper">
                            <figure class="d-flex center">
                                <img src="{{ asset('storage/app/public/' . $person->img) }}" />
                            </figure>
                            <div class="caption text-center m-0">
                                <h4>{{ $person->name }}</h4>
                                <h5>({{ $person->title }})</h5>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ================= ANNOUNCEMENTS ================= --}}
            <div class="container-fluid pt-5">
                <div class="row">
                    <h2 class="fw-bold">
                        <span class="colthemb">U</span><span>-PREPARE</span>
                    </h2>
                </div>

                <div class="row">
                    <div class="col-lg-7 col-xl-8">
                        {{-- Static project intro text (same as before) --}}
                        <p class="text-justify">
                            The Uttarakhand Disaster Resilience and Preparedness Project...
                        </p>
                    </div>

                    <div class="col-lg-5 col-xl-4">
                        <div class="announcement-board h-100">
                            <div class="head text-center">
                                <h3 class="m-0 d-flex align-items-center text-white mb-2">
                                    <img src="{{ asset('assets/img/icons/megaphone-white.png') }}" class="me-2"
                                        alt="Announcements">
                                    {!! request()->cookie('lang') === 'hi' ? 'घोषणा' : 'ANNOUNCEMENTS' !!}
                                </h3>
                            </div>
                            <div class="body p-3">
                                <ul class="list-unstyled m-0">
                                    @foreach (getNews() as $item)
                                        <li class="d-flex align-items-start mb-1 text-black">
                                            <img class="me-2 mt-1" src="{{ asset('assets/img/icons/bullet.png') }}"
                                                alt="•">
                                            <a href="{{ route('news.show', $item->id) }}"
                                                class="text-black text-decoration-none">
                                                {!! request()->cookie('lang') === 'hi' ? $item->title_hi : $item->title_en !!}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= CITIZEN CORNER ================= --}}
    <section class="citizen-corner py-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <h2 class="text-center mb-4">
                        <span class="d-block">CITIZEN CORNER</span>
                        <span class="hr"></span>
                    </h2>
                </div>
            </div>

            <div class="row">
                @foreach ($cc_items as $item)
                    <div class="col-md-4 d-flex justify-content-center mb-4">
                        <a href="{{ $item->link }}" class="cc-item p-4 d-flex align-items-center">
                            <img class="me-2" src="{{ asset('assets/img/icons/' . $item->img) }}">
                            <h6 class="mb-0">{{ $item->name }}</h6>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= PAST PROJECTS ================= --}}
    <section class="past-projects">
        <div class="container-fluid p-0">
            <div class="pps-slider">
                @foreach ($pps_items as $key => $item)
                    <div>
                        <div class="pps-item prel">
                            <div class="bg w-100 h-100">
                                <div class="overlay w-100 h-100 {{ $item->bgc }}"></div>
                                <img class="w-100 h-100" src="{{ asset($item->img) }}">
                            </div>
                            <div
                                class="content d-flex flex-column align-items-center justify-content-between h-100 prel">
                                <h6 class="fw-bold text-white">{{ $item->name }}</h6>
                                <h2 class="fw-bold text-white">{{ $item->title }}</h2>
                                <a href="{{ $item->link }}" @class(['btn', 'btn-pp', 'btn-af' => $key])>
                                    {{ $item->link_txt }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= COMPONENTS ================= --}}
    <section class="components">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="comp-head d-flex align-items-end justify-content-between mb-3">
                        <div class="left mb-1">
                            <h3 class="mb-0">
                                <i class="bi bi-newspaper"></i>
                                COMPONENTS
                            </h3>
                        </div>
                        <div class="right">&nbsp;</div>
                    </div>
                </div>
            </div>

            <div class="components-slider">
                @forelse(getPackageComponents() as $comp)
                    <div>
                        <div class="comps-item">
                            <div class="ci-img">
                                <img src="{{ $comp->image }}" />
                            </div>
                            <div class="ci-content p-2">
                                <h4>
                                    {!! request()->cookie('lang') === 'hi'
                                        ? Str::limit($comp->page_hin_title, 50)
                                        : Str::limit($comp->page_eng_title, 50) !!}
                                </h4>
                                <p class="mb-0">
                                    {!! request()->cookie('lang') === 'hi'
                                        ? Str::limit($comp->hin_content, 500)
                                        : Str::limit($comp->eng_content, 600) !!}
                                </p>
                            </div>
                            <div class="ci-rms text-center pb-3">
                                <a href="{{ $comp->link }}" class="rmore">
                                    <span>READ MORE</span>
                                    <i class="bi bi-caret-right-fill"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                @endforelse
            </div>
        </div>
    </section>

    {{-- ================= VIDEOS ================= --}}
    <section class="videos">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="vid-head d-flex align-items-end justify-content-between mb-3">
                        <div class="left mb-1">
                            <h3 class="mb-0">
                                <i class="bi bi-camera-reels"></i>
                                VIDEOS
                            </h3>
                        </div>
                        <div class="right">
                            <a class="text-white" href="#">
                                <i class="bi bi-chevron-double-right"></i>
                                Click to U-PREPARE-YouTube-channel
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="videos-slider">
                @for ($v = 0; $v < 3; $v++)
                    @foreach (getVideos() as $vid)
                        <div>
                            <div class="vid-item">
                                <div class="vid-img">
                                    <img src="{{ asset('storage/app/public/' . $vid->img) }}" />
                                    <a class="d-flex center" href="#">
                                        <i class="bi bi-play-circle"></i>
                                    </a>
                                </div>
                                <div class="vid-content">
                                    <p class="mb-0">{{ $vid->text }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endfor
            </div>
        </div>
    </section>

    {{-- ================= CONTACT / FEEDBACK ================= --}}
    <section class="contact">
        <div class="contact-head text-center">
            <h3>REACH US</h3>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 col-lg-6 col-xl-5">
                    <a class="text-decoration-underline text-dark" href="#">Find us here</a>
                    <div class="map mb-2">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3442.544367980115!2d78.08280947495017!3d30.36389647476432!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3908d70048644c07%3A0xa0a0da3e097c93a4!2sUSDMA%20New%20Building%20IT%20park!5e0!3m2!1sen!2sin!4v1721896382781!5m2!1sen!2sin"
                            class="w-100 h-100 border-0" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                    <address>
                        <h5>5<sup>th</sup> FLOOR, USDMA NEW BUILDING</h5>
                        <ul class="list-unstyled mb-0">
                            <li>
                                <i class="bi bi-geo-alt-fill"></i>
                                36, IT Park, Dehradun, Uttarakhand, 248013
                            </li>
                            <li>
                                <i class="bi bi-telephone-fill"></i>
                                <a href="tel:18001804276">1800-180-4276</a>,
                                <a href="tel:01352971663">0135-2971663</a>
                            </li>
                            <li>
                                <i class="bi bi-envelope-fill"></i>
                                <a href="mailto:upreparegrievance@gmail.com">upreparegrievance@gmail.com</a>
                            </li>
                        </ul>
                    </address>
                </div>
                <div class="col-xl-2 col-lg-1 d-lg-block d-none"></div>
                <div class="col-md-6 col-xl-4 mt-4 mt-md-0">
                    <h4 class="text-center">
                        <i class="bi bi-chat-left-dots"></i>
                        Feedback
                    </h4>
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('feedback.store') }}" method="POST">
                        @csrf

                        <input type="text" class="form-control mb-3 @error('name') is-invalid @enderror"
                            placeholder="NAME*" name="name" value="{{ old('name') }}">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <input type="email" class="form-control mb-3 @error('email') is-invalid @enderror"
                            placeholder="E-MAIL*" name="email" value="{{ old('email') }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <select name="type" class="form-control mb-3 @error('type') is-invalid @enderror">
                            <option value="">Kindly Select Query Type</option>
                            <option value="inquiry" {{ old('type') == 'inquiry' ? 'selected' : '' }}>INQUIRY</option>
                            <option value="feedback" {{ old('type') == 'feedback' ? 'selected' : '' }}>FEEDBACK
                            </option>
                            <option value="others" {{ old('type') == 'others' ? 'selected' : '' }}>OTHERS</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <input type="text" class="form-control mb-3 @error('subject') is-invalid @enderror"
                            placeholder="SUBJECT" name="subject" value="{{ old('subject') }}">
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <textarea name="message" rows="4" class="form-control mb-3 @error('message') is-invalid @enderror"
                            placeholder="MESSAGE">{{ old('message') }}</textarea>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-theme">Submit</button>
                        </div>
                    </form>


                </div>
                <div class="d-xl-block d-none col-xl-1"></div>
            </div>
        </div>
    </section>
</x-guest-layout>
