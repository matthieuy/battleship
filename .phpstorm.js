/**
 * This file is only for configure the IDE PHPStorm
 */
System.config({
    // Resolve webpack alias
    'paths': {
        '@app/*': './app/Resources/assets/*',
        '@bonus/*': './src/BonusBundle/Resources/assets/*',
        '@chat/*': './src/ChatBundle/Resources/assets/*',
        '@match/*': './src/MatchBundle/Resources/assets/*',
        '@npm/*': './node_modules/*',
        '@user/*': './src/UserBundle/Resources/assets/*',
        '@vendor/*': './vendor/*',
    },
})
