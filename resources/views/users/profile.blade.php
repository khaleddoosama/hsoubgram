<x-app-layout>
    <div
        class="{{ session('success') ? '' : 'hidden' }} w-50 p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800 absolute right-10 shadow shadow-neutral-200"
        role="alert">
        <span class="font-medium">{{ session('success') }}
    </div>
    
    <div class="grid grid-cols-4">
        {{-- User Image --}}
        <div class="px-4 col-span-1 order-1">
            <img src="{{ Str::startsWith($user->image, 'https') ? $user->image : asset('storage/' . $user->image) }}"  alt="{{ $user->username }}' profile picture"
                 class="rounded-full w-20 h-20 object-cover md:w-40 md:h-40 border border-neutral-300">
        </div>

        {{-- Username and buttons --}}
        <div class="px-4 col-span-2 md:ml-0 flex flex-col order-2 md:col-span-3">
            <div class="text-3xl mb-2">{{ $user->username }}</div> <!-- Reduced margin-bottom -->
            @auth
                @if ($user->id === auth()->id())
                    <a href="/profile"
                       class="w-32 h-8 border text-sm font-bold py-1 px-1 rounded-md border-neutral-300 text-center">
                        {{ __('Edit Profile') }}
                    </a>
                @else
                @if(auth()->user()->is_pending($user))
                <span class="w-30 bg-gray-400 text-white px-3 rounded text-center self-start">{{ __('Pending') }}</span>   
                @elseif (auth()->user()->following()->where('following_user_id', $user->id)->exists())
                <!-- Unfollow Button -->
                <form action="{{ route('unfollow', $user) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-30 bg-red-500 text-white px-3 py-1 rounded text-center mt-2">
                        {{ __('Unfollow') }}
                    </button>
                </form>
            @else
                <!-- Follow Button -->
                <form action="{{ route('follow', $user) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-30 bg-blue-400 text-white px-3 py-1 rounded text-center mt-2">
                        {{ __('Follow') }}
                    </button>
                </form>
            @endif
                @endif
            @endauth
            @guest
            <form action="{{ route('follow', $user) }}" method="POST">
                @csrf
                <button type="submit" class="w-30 bg-blue-400 text-white px-3 py-1 rounded text-center mt-2">
                    {{ __('Follow') }}
                </button>
            </form>
            @endguest
        </div>

        {{-- User Bio --}}
        <div class="text-md mt-8 px-4 col-span-3 col-start-1 order-3 md:col-start-2 md:order-4 md:mt-0">
            <p class="font-bold">{{ $user->name }}</p>
            {!! nl2br(e($user->bio)) !!}
        </div>

        {{-- User stats --}}
        <div class="col-span-4 my-5 py-2 border-y border-y-neutral-200 order-4 md:order-3 md:border-none md:px-4 md:col-start-2">
            <ul class="text-md flex flex-row justify-around md:justify-start md:space-x-10 md:text-xl">
                <li class="flex flex-col md:flex-row text-center">
                    <div class="md:mr-2 font-bold md:font-normal">
                        {{ $user->posts->count() }}
                    </div>
                    <span class="text-neutral-500 md:text-black">
                        {{ $user->posts->count() > 1 ? __('posts') : __('post') }}
                    </span>
                </li>


                <li class="flex flex-col md:flex-row text-center">
                    <div class="md:mr-1 font-bold md:font-normal">
                        {{ $user->follower()->where('confirmed',true)->count() }}
                    </div>
                    <span class="text-neutral-500 md:text-black">
                        {{  $user->follower()->where('confirmed',true)->count() > 1 ? __('followers') : __('follower') }}
                    </span>
                </li>


                <li class="flex flex-col md:flex-row text-center">
                    <div class="md:mr-1 font-bold md:font-normal">
                        {{ $user->following()->wherePivot('confirmed',true)->get()->count() }}
                    </div>
                    <span class="text-neutral-500 md:text-black">
                        {{ __('following') }}
                    </span>
                </li>


            </ul>
        </div>
    </div>


    {{-- Bottom --}}

    @if ($user->posts()->count() > 0 && (!$user->private_account || auth()->id() == $user->id || (auth()->check() && auth()->user()->isFollowing($user))))
    <div class="grid grid-cols-3 gap-4 my-5">
        @foreach ($user->posts as $post)
            <a class="aspect-square block w-full" href="/post/{{ $post->slug }}">
                <div class="group relative">
                    <img class="w-full aspect-square object-cover" src="{{ asset('storage/'.$post->image )}}">

                    @if (auth()->id() === $post->user_id)
                        <div class="absolute top-0 left-0 w-full h-full flex justify-center items-center group-hover:bg-black/40">
                            <ul class="invisible group-hover:visible flex flex-row">
                                <li class="flex items-center text-2xl text-white font-bold mr-2">
                                    <i class='bx bxs-heart mr-1'></i>
                                    <span>{{ $post->likes()->count() }}</span>
                                </li>
                                <li class="flex items-center text-2xl text-white font-bold">
                                    <i class='bx bx-comment mr-1'></i>
                                    <span>{{ $post->comments()->count() }}</span>
                                </li>
                            </ul>
                        </div>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
@else
    <div class="w-full text-center mt-20">
        @if ($user->private_account && $user->id != auth()->id())
            {{ __('This Account is Private. Follow to see their photos.') }}
        @else
            {{ __('This user does not have any posts.') }}
        @endif
    </div>
@endif

</x-app-layout>
