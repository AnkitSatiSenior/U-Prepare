@extends('public.layout.base')

@section('page_title'){{ __('Home') }}@endsection

@section('header_stylesheets')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.4/tiny-slider.css">
@endsection

@section('header_styles')
    <style>
        .text-justify {
            text-align: justify;
        }

        .slider-item {
            position: relative;
            min-height: calc(100vh - 207px);
            background-size: cover;
        }

        .hero-slider .slider-item .overlay {
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            position: absolute;
            background-color: rgba(0, 0, 0, 0.45);
        }

        .hero-slider .slider-item .caption {
            left: 8%;
            color: white;
            bottom: 10%;
            position: absolute;
        }

        .hero-slider .slider-item .caption p {
            width: 70%;
        }

        .hero-slider .slider-item .btn {
            background: var(--color-tbbg);
        }

        .tns-outer {
            position: relative;
        }

        .tns-controls {
            top: 50%;
            width: 100%;
            z-index: 1050;
            display: flex;
            transform: translateY(-50%);
            position: absolute;
            align-items: center;
            justify-content: space-between;
        }

        .tns-controls button {
            border: none;
            background: transparent;
        }

        .tns-nav {
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 2;
            display: flex;
            padding: 0;
            position: absolute;
            margin-left: 15%;
            margin-right: 15%;
            margin-bottom: 1rem;
            justify-content: center;
        }

        .tns-nav button {
            width: 12px;
            cursor: pointer;
            border: 0;
            height: 12px;
            opacity: 0.6;
            overflow: hidden;
            transition: 0.3s;
            background: #fff;
            margin-left: 4px;
            margin-right: 4px;
            border-radius: 50px;
            list-style-type: none;
        }

        .tns-nav button.tns-nav-active {
            opacity: 1;
            background: var(--color-tbbg);
        }

        .comp-head {
            border-bottom: 1px solid #4b4b4b;
        }

        .comp-head .right a i {
            font-size: 0.8em;
        }

        .components .tns-nav {
            bottom: -30px;
        }

        .components .tns-nav button {
            opacity: 1;
            background: #c4c5c5;
        }

        .components .tns-nav button.tns-nav-active {
            background: #040404;
        }

        .components-slider .tns-item .comps-item {
            margin:0 2px;
        }

        .components-slider .tns-item:first-child .comps-item {
            margin-left: 0;
        }

        .components-slider .tns-item:last-child .comps-item {
            margin-right: 0;
        }

        .ci-img {
            position: relative;
            min-height: 350px;
        }

        .ci-img > img {
            width: 100%;
            height: 100%;
            position: absolute;
            object-fit: fill;
        }

        .ci-content h4 {
            font-size: 1em;
            font-weight: bold;
        }

        .ci-content p {
            font-size: 0.8em;
            text-align: justify
        }

        .videos {
            color: white;
            background: #197eb8;
        }

        .vid-head {
            border-bottom: 1px solid #fff;
        }

        .vid-head .right a i {
            font-size: 0.8em;
        }

        .videos .tns-nav {
            bottom: -50px;
        }

        .videos .tns-nav button {
            opacity: 1;
            background: #f4f4ff;
        }

        .videos .tns-nav button.tns-nav-active {
            background: #054366;
        }

        .videos-slider .tns-item .vid-item {
            margin:0 5px;
        }

        .videos-slider .tns-item:first-child .vid-item {
            margin-left: 0;
        }

        .videos-slider .tns-item:last-child .vid-item {
            margin-right: 0;
        }

        .videos-slider .vid-img {
            width: 100%;
            position: relative;
            padding-bottom: calc(9 / 16 * 100%);
        }

        .videos-slider .vid-img > img {
            width: 100%;
            height: 100%;
            position: absolute;
            object-fit: fill;
        }

        .vid-img a {
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            position: absolute;
        }

        .vid-img a i {
            color: white;
            font-size: 64px;
        }

        .contact-head {
            position: relative;
        }

        .contact-head h3 {
            z-index: 2;
            display: inline;
            padding: 0 10px;
            position: relative;
            background: white;
            font-weight: bold;
        }

        .contact-head::after {
            top:50%;
            left:50%;
            width: 100%;
            content:'';
            display:block;
            height:1px;
            z-index:1;
            position:absolute;
            transform:translateX(-50%);
            background:#000;
            -webkit-transform:translateX(-50%);
        }

        .contact .map {
            width: 100%;
            height: 320px;
        }
    </style>
@endsection

@section('page_content')
    @php
        $slides = [
            (object) [
                'img'     => 'assets/public/img/carousel/IMG20240426151005.jpg',
                'head'    => 'Building a safer future',
                'subh'    => 'Empowering communities with innovative distaer preparedness and resilient recovery. Together, we protect lives and ensure swift, effective response.',
                'link'    => '#',
                'btn_text'=> 'Read Story'
            ],
            (object) [
                'img'     => 'assets/public/img/carousel/IMG20240426150955.jpg',
                'head'    => NULL,
                'subh'    => NULL,
                'link'    => NULL,
                'btn_text'=> NULL
            ],
            (object) [
                'img'     => 'assets/public/img/carousel/one.jpeg',
                'head'    => NULL,
                'subh'    => NULL,
                'link'    => NULL,
                'btn_text'=> NULL
            ],
            (object) [
                'img'     => 'assets/public/img/carousel/two.jpeg',
                'head'    => NULL,
                'subh'    => NULL,
                'link'    => NULL,
                'btn_text'=> NULL
            ],
        ];
    @endphp

    <section class="slider p-0">
        <div class="hero-slider">
            @foreach($slides as $slide)
                <div>
                    <div class="slider-item" style="background-image: url({{ asset($slide->img) }})">
                        <div class="overlay"></div>
                        <div class="caption">
                            @if($slide->head)
                                <h2>{{ $slide->head }}</h2>
                            @endif
                            @if($slide->subh)
                                <p class="mb-3">{{ $slide->subh }}</p>
                            @endif
                            @if($slide->link)
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
    </section><!-- End Hero -->

    <!-- ======= Section ======= -->
    <section id="about" class="about">
        <div class="container-fluid">

            <div class="section-title">
                <h2>Who We Are</h2>
            </div>

            <div class="content">
                <p class="text-justify">
                    We are committed to enhancing the climate and disaster resilience of critical public infrastructure in Uttarakhand.
                    Our objectives include improving infrastructure access, developing early warning systems, and establishing fire stations
                    to benefit communities. We focus on enhancing infrastructure resilience, boosting emergency preparedness, preventing and
                    managing forest fires, and ensuring efficient project management. To achieve these goals, we conduct capacity building and
                    training for local authorities and communities, promote awareness of disaster risk reduction, and leverage innovative technologies
                    for risk assessment. Collaborating with governmental, non-governmental, and international partners is key to our approach, ensuring
                    a comprehensive effort to safeguard lives and livelihoods. By integrating resilience into development planning, we aim to create a
                    safer, more resilient Uttarakhand for future generations.
                </p>
            </div>
        </div>
    </section><!-- End Section -->

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
                        <div class="right">
                            <a class="text-dark" href="#">
                                <i class="bi bi-chevron-double-right"></i>
                                Read more
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @php
                $comps = [
                    (object) [
                        'img'=> 'assets/public/img/comps/width_800.jpeg',
                        'head'=> 'Enhancing Infrastructure Resilience',
                        'desc'=> 'This initiative strengthens infrastructure against climate and disaster risks by enhancing road resilience,
                                upgrading health facilities for safety, and building energy-efficient disaster shelters. Key actions include
                                reinforcing bridges, mitigating landslides with bioengineering, and retrofitting health centers. These efforts
                                ensure better protection against environmental hazards in Uttarakhand.',
                        'link'=> '#',
                    ],
                    (object) [
                        'img'=> 'assets/public/img/comps/width_610.webp',
                        'head'=> 'Improving Emergency Preparedness and Response',
                        'desc'=> 'Enhances disaster prediction, preparedness, and response with a State Emergency Operations Centre, improved
                                multi-agency coordination, community DRM campaigns, upgraded hydromet systems for accuracy, timely alerts,
                                strengthened State Disaster Response Force with training facilities, equipment, and comprehensive programs.',
                        'link'=> '',
                    ],
                    (object) [
                        'img'=> 'assets/public/img/comps/width_480.webp',
                        'head'=> 'Preventing and Managing Forest and General Fires',
                        'desc'=> 'Enhancing fire prevention and management capacities includes fire risk assessments, state-level fire management
                                plans, advanced firefighting technologies, and community-based initiatives. Efforts focus on early detection, policy
                                assessments, equipment procurement, and incentive-based programs for sustainable forest management. These measures
                                mitigate fire risks, promote biodiversity, and ensure long-term sustainability.',
                        'link'=> '',
                    ],
                    (object) [
                        'img'=> 'assets/public/img/comps/width_800_b.jpeg',
                        'head'=> 'Project Management',
                        'desc'=> 'Facilitates project management and knowledge sharing through financial management, procurement, environmental
                                and social management, communication, monitoring, evaluation, and stakeholder engagement. Establishes Lighthouse
                                Uttarakhand for sharing project lessons, capacity building, and knowledge exchange among Indian states.',
                        'link'=> '',
                    ],
                ];
            @endphp

            <div class="components-slider">
                @for($i=0;$i<2;$i++)
                    @foreach ($comps as $comp)
                        <div>
                            <div class="comps-item">
                                <div class="ci-img">
                                    <img src="{{ asset($comp->img) }}" />
                                </div>
                                <div class="ci-content p-2">
                                    <h4>{{ $comp->head }}</h4>
                                    <p class="mb-0">{{ $comp->desc }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endfor
            </div>
        </div>
    </section>

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

            @php
                $vids = [
                    (object) [
                        'img'=> 'assets/public/img/videos/width_200b.webp',
                        'text'=> '6th World Congress_Lieutenant General Gurmit Singh Hon\'ble Governor Govt of Uttarakhand'
                    ],
                    (object) [
                        'img'=> 'assets/public/img/videos/width_200.webp',
                        'text'=> '6th World Congress on Disaster Management Dehradun 28th Nov to 1st Dec 2023_Shri Amitabh Bachchan@1'
                    ],
                    (object) [
                        'img'=> 'assets/public/img/videos/width_200c.webp',
                        'text'=> '6th World Congress on Disaster Management Dehradun_ Dr Ranjit Kumar Sinha, Secretary_4, USDMA'
                    ],
                    (object) [
                        'img'=> 'assets/public/img/videos/width_200d.webp',
                        'text'=> 'Uttarakhand State Disaster Management Authority Live Stream'
                    ],
                ];
            @endphp

            <div class="videos-slider">
                @for($v=0;$v<3;$v++)
                    @foreach($vids as $vid)
                        <div>
                            <div class="vid-item">
                                <div class="vid-img">
                                    <img src="{{ asset($vid->img) }}" />
                                    <a class="d-flex align-items-center justify-content-center" href="#">
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

    <section class="contact">
        <div class="contact-head text-center">
            <h3>REACH US</h3>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-5">
                    <a class="text-decoration-underline text-dark" href="#">Find us here</a>
                    <div class="map">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3442.544367980115!2d78.08280947495017!3d30.36389647476432!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3908d70048644c07%3A0xa0a0da3e097c93a4!2sUSDMA%20New%20Building%20IT%20park!5e0!3m2!1sen!2sin!4v1721896382781!5m2!1sen!2sin" class="w-100 h-100 border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                    <address>
                        <h5>USDMA NEW BUILDING</h5>
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
                <div class="col-lg-2"></div>
                <div class="col-lg-4">
                    <h4 class="text-center">
                        <i class="bi bi-chat-left-dots"></i>
                        Feedback
                    </h4>
                    <form action="">
                        <input type="text" class="form-control mb-3" placeholder="NAME*">
                        <input type="text" class="form-control mb-3" placeholder="E-MAIL*">
                        <select name="" id="" class="form-control mb-3">
                            <option value="">Kindly Select Query Type</option>
                            <option value="inquiry">INQUIRY</option>
                            <option value="feedback">FEEDBACK</option>
                            <option value="others">OTHERS</option>
                        </select>
                        <input type="text" class="form-control mb-3" placeholder="SUBJECT">
                        <textarea name="" rows="4" class="form-control mb-3" placeholder="MESSAGE"></textarea>

                        <div class="d-flex justify-content-end">
                            <button class="btn btn-theme">Submit</button>
                        </div>
                    </form>
                </div>
                <div class="col-lg-1"></div>
            </div>
        </div>
    </section>
@endsection

@section('footer_scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.2/min/tiny-slider.js"></script>
@endsection

@section('inpage_scripts')
    var homeSlider = tns({
        items: 1,
        slideBy: 'page',
        autoplay: true,
        container: '.hero-slider',
        navPosition: 'bottom',
        autoplayButton: false,
        autoplayButtonOutput: false,
        controlsText: [
            '<span class="carousel-control-prev-icon bi bi-chevron-left" aria-hidden="true"></span>',
            '<span class="carousel-control-next-icon bi bi-chevron-right" aria-hidden="true"></span>'
        ]
    });

    var homeSlider = tns({
        items: 4,
        slideBy: 'page',
        autoplay: true,
        controls: false,
        container: '.components-slider',
        navPosition: 'bottom',
        autoplayButton: false,
        autoplayButtonOutput: false,
    })

    var homeSlider = tns({
        items: 4,
        slideBy: 'page',
        autoplay: true,
        controls: false,
        container: '.videos-slider',
        navPosition: 'bottom',
        autoplayButton: false,
        autoplayButtonOutput: false,
    })
@endsection
