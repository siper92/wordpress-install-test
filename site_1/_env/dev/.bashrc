case $- in
    *i*) ;;
      *) return;;
esac

HISTCONTROL=ignoreboth
shopt -s histappend
HISTSIZE=1000
HISTFILESIZE=2000
shopt -s checkwinsize

if ! shopt -oq posix; then
  if [ -f /usr/share/bash-completion/bash_completion ]; then
    . /usr/share/bash-completion/bash_completion
  elif [ -f /etc/bash_completion ]; then
    . /etc/bash_completion
  fi
fi

# prompt style
PS1='\[\033[01;32m\]\u\[\033[00m\]:\[\033[01;34m\]\w\[\033[00m\]\$ '

# enable color support of ls and also add handy aliases
if [ -x /usr/bin/dircolors ]; then
    test -r ~/.dircolors && eval "$(dircolors -b ~/.dircolors)" || eval "$(dircolors -b)"
    alias ls='ls --color=auto'
    alias grep='grep --color=auto'
fi

# if directory exists add to path
mkdir -p "$HOME/bin"
PATH="$HOME/bin:$PATH"

# Key bindings, up/down arrow searches through history
bind '"\e[A": history-search-backward'
bind '"\e[B": history-search-forward'
bind '"\eOA": history-search-backward'
bind '"\eOB": history-search-forward'
# jump words with left and right
bind '"\e[1;5D" backward-word'
bind '"\e[1;5C" forward-word'

# >>>>>>>>>>>>>>>>>>>>>>>> CUSTOM <<<<<<<<<<<<<<<<<<<<<<
alias refresh-bash='source ~/.bashrc'
alias ww='cd /app'
alias ll='ls -ahlrt'
alias build-time='cat /build-timestamp'
#########################################################
