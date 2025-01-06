# ConsoleGpt - Symfony GPT Console Assistant

This library allows seamless integration of a GPT-based chat interface with `symfony/console`, enabling users to execute one command or a sequence of commands and manipulate them interactively via chat.

**Have you dreamed of speaking the same language with your application? 😀**

![](docs/assets/demo.gif)

*Try chatting with the demo app*

```bash
$ OPENAI_API_KEY='<your API key>' php demo/demo.php chat
```

## Features

- **Run Console Commands**: Execute a single Symfony console command or a sequence of commands through an interactive chat.
- **Processing the result**: Dynamically change and modify the return result of console commands. If you want to display it in the form of a table, please do so. Translating into any language is not a problem.
- **Easy to use**: Integrate ConsoleGpt into your existing Symfony console application and start talking to it today!

## Installation

1. Add the library to your project via Composer:

    ```bash
    composer require shcherbanich/symfony-console-gpt
    ```

2. Ensure your project uses Symfony Console (`symfony/console`).

3. Add the `Chat` command to your Symfony console application.

4. Set the environment variable `OPENAI_API_KEY`.

## Usage

### Basic Setup

The library provides a ready-to-use command: `ConsoleGpt\Command\ChatCommand`. You can use it in your project in two ways:

1. **Add the Command to Your Application**  
   Include the `ChatCommand` in your console application:

    ```php
    use ConsoleGpt\Command\ChatCommand;
    use Symfony\Component\Console\Application;

    $application = new Application();
    $application->add(new ChatCommand());
    $application->run();
    ```

2. **Inherit from the Chat Application**  

    ```php
    namespace MyApp\Console;

    use ConsoleGpt\Application as ChatApplication;

    final class SomeApp extends ChatApplication
    {
        // Add custom behavior here
    }
    ```

In this case, the command will be available in your application.

### Running the Chat Command

Run the `chat` command in your console:

```bash
$ OPENAI_API_KEY='<your API key>' php console chat
```

and enjoy the magic :)
