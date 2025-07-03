<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sign Up - Yazamos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f7f7f7;
            /* Light gray background */
            font-family: 'Roboto', sans-serif;
        }

        .container {
            max-width: 500px;
            margin: 60px auto;
            background-color: #fff;
            /* White background for the form */
            padding: 40px 50px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            /* Subtle shadow */
            background: #fff;
            /* White background */
        }

        h6 {
            text-align: center;
            color: #333;
            /* Dark gray for heading text */
            margin-bottom: 30px;
            font-weight: 400;
            font-size: 18px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            color: #555;
            /* Darker gray for label text */
            margin-bottom: 8px;
            font-weight: bold;
        }

        input,
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            /* Light gray border */
            border-radius: 8px;
            margin-top: 6px;
            font-size: 14px;
            outline: none;
            background-color: #f9f9f9;
            /* Very light gray input background */
        }

        input:focus,
        select:focus {
            border-color: #aaa;
            /* Gray focus border */
            box-shadow: 0 0 5px rgba(170, 170, 170, 0.5);
            /* Subtle focus shadow */
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            top: 12px;
            left: 15px;
            color: #aaa;
            /* Light gray for icon */
        }

        .error {
            background: #f8d7da;
            /* Light red background */
            border: 1px solid #f5c6cb;
            /* Light red border */
            color: #721c24;
            /* Dark red text */
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-size: 14px;
        }

        button {
            background-color: #ccc;
            /* Light gray button */
            color: #333;
            /* Dark gray text */
            padding: 14px;
            width: 100%;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #aaa;
            /* Darker gray on hover */
        }

        .login-link {
            margin-top: 20px;
            text-align: center;
            font-size: 14px;
        }

        .login-link a {
            color: #333;
            /* Dark gray for the link */
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .logo {
            width: 100px;
            margin-bottom: 20px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
    </style>

</head>

<body>

    <div class="container">
        <img src="{{ asset('images/logo.png') }}" alt="Yazamos Logo" class="logo">

        <h6>Create Your Account</h6>

        @php
            $countryCodes = [
                '+93' => 'Afghanistan (+93)',
                '+358' => 'Aland Islands (+358)',
                '+355' => 'Albania (+355)',
                '+213' => 'Algeria (+213)',
                '+1684' => 'American Samoa (+1684)',
                '+376' => 'Andorra (+376)',
                '+244' => 'Angola (+244)',
                '+1264' => 'Anguilla (+1264)',
                '+672' => 'Antarctica (+672)',
                '+1268' => 'Antigua and Barbuda (+1268)',
                '+54' => 'Argentina (+54)',
                '+374' => 'Armenia (+374)',
                '+297' => 'Aruba (+297)',
                '+61' => 'Australia (+61)',
                '+43' => 'Austria (+43)',
                '+994' => 'Azerbaijan (+994)',
                '+1242' => 'Bahamas (+1242)',
                '+973' => 'Bahrain (+973)',
                '+880' => 'Bangladesh (+880)',
                '+1246' => 'Barbados (+1246)',
                '+375' => 'Belarus (+375)',
                '+32' => 'Belgium (+32)',
                '+501' => 'Belize (+501)',
                '+229' => 'Benin (+229)',
                '+1441' => 'Bermuda (+1441)',
                '+975' => 'Bhutan (+975)',
                '+591' => 'Bolivia (+591)',
                '+599' => 'Bonaire, Sint Eustatius and Saba (+599)',
                '+387' => 'Bosnia and Herzegovina (+387)',
                '+267' => 'Botswana (+267)',
                '+55' => 'Bouvet Island (+55)',
                '+55' => 'Brazil (+55)',
                '+246' => 'British Indian Ocean Territory (+246)',
                '+673' => 'Brunei Darussalam (+673)',
                '+359' => 'Bulgaria (+359)',
                '+226' => 'Burkina Faso (+226)',
                '+257' => 'Burundi (+257)',
                '+855' => 'Cambodia (+855)',
                '+237' => 'Cameroon (+237)',
                '+1' => 'Canada (+1)',
                '+238' => 'Cape Verde (+238)',
                '+1345' => 'Cayman Islands (+1345)',
                '+236' => 'Central African Republic (+236)',
                '+235' => 'Chad (+235)',
                '+56' => 'Chile (+56)',
                '+86' => 'China (+86)',
                '+61' => 'Christmas Island (+61)',
                '+672' => 'Cocos (Keeling) Islands (+672)',
                '+57' => 'Colombia (+57)',
                '+269' => 'Comoros (+269)',
                '+242' => 'Congo (+242)',
                '+242' => 'Congo, Democratic Republic of the Congo (+242)',
                '+682' => 'Cook Islands (+682)',
                '+506' => 'Costa Rica (+506)',
                '+225' => 'Cote D\'Ivoire (+225)',
                '+385' => 'Croatia (+385)',
                '+53' => 'Cuba (+53)',
                '+599' => 'Curacao (+599)',
                '+357' => 'Cyprus (+357)',
                '+420' => 'Czech Republic (+420)',
                '+45' => 'Denmark (+45)',
                '+253' => 'Djibouti (+253)',
                '+1767' => 'Dominica (+1767)',
                '+1809' => 'Dominican Republic (+1809)',
                '+593' => 'Ecuador (+593)',
                '+20' => 'Egypt (+20)',
                '+503' => 'El Salvador (+503)',
                '+240' => 'Equatorial Guinea (+240)',
                '+291' => 'Eritrea (+291)',
                '+372' => 'Estonia (+372)',
                '+251' => 'Ethiopia (+251)',
                '+500' => 'Falkland Islands (Malvinas) (+500)',
                '+298' => 'Faroe Islands (+298)',
                '+679' => 'Fiji (+679)',
                '+358' => 'Finland (+358)',
                '+33' => 'France (+33)',
                '+594' => 'French Guiana (+594)',
                '+689' => 'French Polynesia (+689)',
                '+262' => 'French Southern Territories (+262)',
                '+241' => 'Gabon (+241)',
                '+220' => 'Gambia (+220)',
                '+995' => 'Georgia (+995)',
                '+49' => 'Germany (+49)',
                '+233' => 'Ghana (+233)',
                '+350' => 'Gibraltar (+350)',
                '+30' => 'Greece (+30)',
                '+299' => 'Greenland (+299)',
                '+1473' => 'Grenada (+1473)',
                '+590' => 'Guadeloupe (+590)',
                '+1671' => 'Guam (+1671)',
                '+502' => 'Guatemala (+502)',
                '+44' => 'Guernsey (+44)',
                '+224' => 'Guinea (+224)',
                '+245' => 'Guinea-Bissau (+245)',
                '+592' => 'Guyana (+592)',
                '+509' => 'Haiti (+509)',
                '+39' => 'Holy See (Vatican City State) (+39)',
                '+504' => 'Honduras (+504)',
                '+852' => 'Hong Kong (+852)',
                '+36' => 'Hungary (+36)',
                '+354' => 'Iceland (+354)',
                '+91' => 'India (+91)',
                '+62' => 'Indonesia (+62)',
                '+98' => 'Iran, Islamic Republic of (+98)',
                '+964' => 'Iraq (+964)',
                '+353' => 'Ireland (+353)',
                '+44' => 'Isle of Man (+44)',
                '+972' => 'Israel (+972)',
                '+39' => 'Italy (+39)',
                '+1876' => 'Jamaica (+1876)',
                '+81' => 'Japan (+81)',
                '+44' => 'Jersey (+44)',
                '+962' => 'Jordan (+962)',
                '+7' => 'Kazakhstan (+7)',
                '+254' => 'Kenya (+254)',
                '+686' => 'Kiribati (+686)',
                '+850' => 'Korea, Democratic People\'s Republic of (+850)',
                '+82' => 'Korea, Republic of (+82)',
                '+381' => 'Kosovo (+381)',
                '+965' => 'Kuwait (+965)',
                '+996' => 'Kyrgyzstan (+996)',
                '+856' => 'Lao People\'s Democratic Republic (+856)',
                '+371' => 'Latvia (+371)',
                '+961' => 'Lebanon (+961)',
                '+266' => 'Lesotho (+266)',
                '+231' => 'Liberia (+231)',
                '+218' => 'Libyan Arab Jamahiriya (+218)',
                '+423' => 'Liechtenstein (+423)',
                '+370' => 'Lithuania (+370)',
                '+352' => 'Luxembourg (+352)',
                '+853' => 'Macao (+853)',
                '+389' => 'Macedonia, the Former Yugoslav Republic of (+389)',
                '+261' => 'Madagascar (+261)',
                '+265' => 'Malawi (+265)',
                '+60' => 'Malaysia (+60)',
                '+960' => 'Maldives (+960)',
                '+223' => 'Mali (+223)',
                '+356' => 'Malta (+356)',
                '+692' => 'Marshall Islands (+692)',
                '+596' => 'Martinique (+596)',
                '+222' => 'Mauritania (+222)',
                '+230' => 'Mauritius (+230)',
                '+262' => 'Mayotte (+262)',
                '+52' => 'Mexico (+52)',
                '+691' => 'Micronesia, Federated States of (+691)',
                '+373' => 'Moldova, Republic of (+373)',
                '+377' => 'Monaco (+377)',
                '+976' => 'Mongolia (+976)',
                '+382' => 'Montenegro (+382)',
                '+1664' => 'Montserrat (+1664)',
                '+212' => 'Morocco (+212)',
                '+258' => 'Mozambique (+258)',
                '+95' => 'Myanmar (+95)',
                '+264' => 'Namibia (+264)',
                '+674' => 'Nauru (+674)',
                '+977' => 'Nepal (+977)',
                '+31' => 'Netherlands (+31)',
                '+599' => 'Netherlands Antilles (+599)',
                '+687' => 'New Caledonia (+687)',
                '+64' => 'New Zealand (+64)',
                '+505' => 'Nicaragua (+505)',
                '+227' => 'Niger (+227)',
                '+234' => 'Nigeria (+234)',
                '+683' => 'Niue (+683)',
                '+672' => 'Norfolk Island (+672)',
                '+1670' => 'Northern Mariana Islands (+1670)',
                '+47' => 'Norway (+47)',
                '+968' => 'Oman (+968)',
                '+92' => 'Pakistan (+92)',
                '+680' => 'Palau (+680)',
                '+970' => 'Palestinian Territory, Occupied (+970)',
                '+507' => 'Panama (+507)',
                '+675' => 'Papua New Guinea (+675)',
                '+595' => 'Paraguay (+595)',
                '+51' => 'Peru (+51)',
                '+63' => 'Philippines (+63)',
                '+48' => 'Poland (+48)',
                '+351' => 'Portugal (+351)',
                '+1787' => 'Puerto Rico (+1787)',
                '+974' => 'Qatar (+974)',
                '+262' => 'Reunion (+262)',
                '+40' => 'Romania (+40)',
                '+7' => 'Russian Federation (+7)',
                '+250' => 'Rwanda (+250)',
                '+590' => 'Saint Barthelemy (+590)',
                '+290' => 'Saint Helena, Ascension and Tristan da Cunha (+290)',
                '+1869' => 'Saint Kitts and Nevis (+1869)',
                '+1758' => 'Saint Lucia (+1758)',
                '+590' => 'Saint Martin (+590)',
                '+590' => 'Saint Pierre and Miquelon (+590)',
                '+1784' => 'Saint Vincent and the Grenadines (+1784)',
                '+684' => 'Samoa (+684)',
                '+378' => 'San Marino (+378)',
                '+239' => 'Sao Tome and Principe (+239)',
                '+966' => 'Saudi Arabia (+966)',
                '+221' => 'Senegal (+221)',
                '+381' => 'Serbia (+381)',
                '+248' => 'Seychelles (+248)',
                '+232' => 'Sierra Leone (+232)',
                '+65' => 'Singapore (+65)',
                '+421' => 'Slovakia (+421)',
                '+386' => 'Slovenia (+386)',
                '+677' => 'Solomon Islands (+677)',
                '+252' => 'Somalia (+252)',
                '+27' => 'South Africa (+27)',
                '+82' => 'South Korea (+82)',
                '+211' => 'South Sudan (+211)',
                '+34' => 'Spain (+34)',
                '+94' => 'Sri Lanka (+94)',
                '+249' => 'Sudan (+249)',
                '+597' => 'Suriname (+597)',
                '+47' => 'Svalbard and Jan Mayen (+47)',
                '+268' => 'Swaziland (+268)',
                '+46' => 'Sweden (+46)',
                '+41' => 'Switzerland (+41)',
                '+963' => 'Syrian Arab Republic (+963)',
                '+886' => 'Taiwan (+886)',
                '+992' => 'Tajikistan (+992)',
                '+255' => 'Tanzania, United Republic of (+255)',
                '+66' => 'Thailand (+66)',
                '+228' => 'Togo (+228)',
                '+690' => 'Tokelau (+690)',
                '+676' => 'Tonga (+676)',
                '+1' => 'Trinidad and Tobago (+1)',
                '+216' => 'Tunisia (+216)',
                '+90' => 'Turkey (+90)',
                '+993' => 'Turkmenistan (+993)',
                '+1649' => 'Turks and Caicos Islands (+1649)',
                '+688' => 'Tuvalu (+688)',
                '+256' => 'Uganda (+256)',
                '+380' => 'Ukraine (+380)',
                '+971' => 'United Arab Emirates (+971)',
                '+44' => 'United Kingdom (+44)',
                '+1' => 'United States (+1)',
                '+598' => 'Uruguay (+598)',
                '+998' => 'Uzbekistan (+998)',
                '+678' => 'Vanuatu (+678)',
                '+379' => 'Vatican City (+379)',
                '+58' => 'Venezuela (+58)',
                '+84' => 'Viet Nam (+84)',
                '+1284' => 'Virgin Islands (+1284)',
                '+681' => 'Wallis and Futuna (+681)',
                '+212' => 'Western Sahara (+212)',
                '+967' => 'Yemen (+967)',
                '+260' => 'Zambia (+260)',
                '+263' => 'Zimbabwe (+263)',
            ];

        @endphp

        <form method="POST" action="{{ route('signup.submit', ['role' => request()->route('role')]) }}">
            @csrf

            <!-- Full Name Field -->
            <div class="form-group input-wrapper">
                <label for="name">Full Name</label>
                <i class="fas fa-user"></i>
                <input type="text" name="name" id="name" placeholder="Enter your full name" required
                    value="{{ old('name') }}">
                @if ($errors->has('name'))
                    <div style="color: red">{{ $errors->first('name') }}</div>
                @endif
            </div>

            <!-- Email Field -->
            <div class="form-group input-wrapper">
                <label for="email">Email</label>
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" id="email" placeholder="Enter your email" required
                    value="{{ old('email') }}">
                @if ($errors->has('email'))
                    <div style="color: red">{{ $errors->first('email') }}</div>
                @endif
            </div>

            <!-- Country Code Field -->
            <div class="form-group input-wrapper">
                <label for="country_code">Country Code</label>
                <select name="country_code" id="country_code" class="form-control">
                    @foreach ($countryCodes as $code => $label)
                        <option value="{{ $code }}"
                            {{ old('country_code', $country_code) == $code ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @if ($errors->has('country_code'))
                    <div class="invalid-feedback d-block" style="color: red">{{ $errors->first('country_code') }}</div>
                @endif
            </div>

            <!-- Phone Number Field -->
            <div class="form-group input-wrapper">
                <label for="phone">Phone Number</label>
                <i class="fas fa-phone"></i>
                <input type="text" name="phone" id="phone" class="form-control"
                    placeholder="Enter your phone number" required value="{{$phone}}">
                @if ($errors->has('phone'))
                    <div class="invalid-feedback d-block" style="color: red">{{ $errors->first('phone') }}</div>
                @endif
            </div>


            <!-- Password Field -->
            <div class="form-group input-wrapper">
                <label for="password">Password</label>
                <i class="fas fa-lock"></i>
                <input type="password" name="password" id="password" placeholder="Enter your password" required>
                @if ($errors->has('password'))
                    <div style="color: red">{{ $errors->first('password') }}</div>
                @endif
            </div>

            <!-- Password Confirmation Field -->
            <div class="form-group input-wrapper">
                <label for="password_confirmation">Confirm Password</label>
                <i class="fas fa-lock"></i>
                <input type="password" name="password_confirmation" id="password_confirmation"
                    placeholder="Confirm your password" required>
                @if ($errors->has('password_confirmation'))
                    <div style="color: red">{{ $errors->first('password_confirmation') }}</div>
                @endif
            </div>

            <button type="submit">Sign Up</button>
        </form>

    </div>

    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</body>

</html>
