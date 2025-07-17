  <header class="navbar bg-base-100 shadow-lg sticky top-0 z-50">
      <div class="navbar-start">
          <div class="dropdown">
              <div tabindex="0" role="button" class="btn btn-ghost lg:hidden">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16">
                      </path>
                  </svg>
              </div>

              <ul tabindex="0"
                  class="menu menu-sm dropdown-content bg-base-100 rounded-box z-[1] mt-3 w-52 p-2 shadow">


                  @foreach ($navLinks as $navLink)
                      {{-- Skip si label et logo sont tous les deux vides --}}
                      @unless (($navLink['label'] ?? '') === '' && ($navLink['icon'] ?? '') === '')
                          <li>
                              <a href="{{ $navLink['href'] ?? '#' }}">
                                  {!! $navLink['icon'] ?? '' !!}
                                  {{ $navLink['label'] ?? '' }}
                              </a>
                          </li>
                      @endunless
                  @endforeach

                  {{-- Divider --}}
                  <li>
                      <hr class="my-2">
                  </li>
                  {{-- Auth menu mobile --}}
                  <x-app.auth.menu-mobile />

              </ul>
          </div>
          <a href="{{ route('accueil') }}" class="btn btn-ghost text-xl text-primary font-bold flex items-center">
              <x-icons.home-site />
          </a>
      </div>

      <div class="navbar-center hidden lg:flex">
          <ul class="menu menu-horizontal px-1">


              @foreach ($navLinks as $navLink)
                  @unless (($navLink['label'] ?? '') === '' && ($navLink['icon'] ?? '') === '')
                      <li>
                          <a href="{{ $navLink['href'] ?? '#' }}" class="btn btn-ghost">
                              {!! $navLink['icon'] ?? '' !!}
                              {{ $navLink['label'] ?? '' }}
                          </a>
                      </li>
                  @endunless
              @endforeach
          </ul>
      </div>

      <div class="navbar-end">
          <x-app.auth.menu />
      </div>
  </header>
